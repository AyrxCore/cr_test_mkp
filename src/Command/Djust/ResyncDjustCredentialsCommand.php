<?php

declare(strict_types=1);

namespace App\Command\Djust;

use App\Service\Djust\DjustCredentialResyncService;
use App\Service\Djust\DjustOperatorApiService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:resync-djust-credentials',
    description: 'Resync Djust IDs from Djust preprod API into Marketplace accounts (after prod dump)',
)]
class ResyncDjustCredentialsCommand extends Command
{
    public function __construct(
        private readonly DjustCredentialResyncService $resyncService,
        private readonly DjustOperatorApiService $operatorApi,
        private readonly LoggerInterface $logger,
        #[Autowire(env: 'default::DJUST_TEST_ACCOUNTS_PASSWORD')]
        private readonly string $testAccountsPassword = '',
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Simulate without writing to Marketplace database')
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'Target a single account by its Djust username (email)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = (bool) $input->getOption('dry-run');
        $targetEmail = $input->getOption('email');
        $effectiveTestPassword = $this->testAccountsPassword;

        $io->title('Resync Djust credentials: Djust preprod API → Marketplace');

        if ($dryRun) {
            $io->warning('DRY-RUN mode — no changes will be written to the Marketplace database.');
        }

        if (!$this->operatorApi->isConfigured()) {
            $io->error('DJUST_API_BASE_URL, DJUST_API_USERNAME or DJUST_API_PASSWORD is not configured.');

            return Command::FAILURE;
        }

        try {
            $this->operatorApi->getOperatorToken();
            $io->info('Operator token obtained successfully.');
        } catch (\Throwable $e) {
            $io->error('Failed to obtain Djust operator token: '.$e->getMessage());

            return Command::FAILURE;
        }

        $io->info('Fetching customer-users from Djust preprod API…');

        try {
            $djustUsers = $this->resyncService->fetchDjustPreprodUsers();
        } catch (\Throwable $e) {
            $io->error('Failed to fetch customer-users from Djust API: '.$e->getMessage());

            return Command::FAILURE;
        }

        if (empty($djustUsers)) {
            $io->warning('No customer-users found in Djust preprod. Nothing to do.');

            return Command::SUCCESS;
        }

        // Filter to a single email if --email was provided
        if ($targetEmail !== null) {
            $normalizedTarget = \strtolower($targetEmail);
            $djustUsers = \array_values(\array_filter(
                $djustUsers,
                static fn (array $u): bool => \strtolower($u['email']) === $normalizedTarget,
            ));

            if (empty($djustUsers)) {
                $io->warning(\sprintf('No Djust user found with email "%s". Nothing to do.', $targetEmail));

                return Command::SUCCESS;
            }

            $io->info(\sprintf('Targeting single account: %s', $targetEmail));
        } else {
            $io->info(\sprintf('Fetched %d customer-user(s) from Djust preprod.', \count($djustUsers)));
        }

        $io->info('Loading local accounts with djust_username…');
        $localIndex = $this->resyncService->buildLocalEmailIndex();
        $io->info(\sprintf('Loaded %d local account(s) with djust_username.', \count($localIndex)));

        $pendingUpdates = [];
        $attempted = 0;
        $notInLocal = 0;

        foreach ($djustUsers as $djustUser) {
            $djustEmail = \strtolower($djustUser['email']);
            $lookupEmail = $this->resyncService->stripTestTag($djustEmail);
            $isTestEmail = $djustEmail !== $lookupEmail;

            $accountId = $localIndex[$lookupEmail] ?? null;

            if ($accountId === null) {
                ++$notInLocal;
                continue;
            }

            // Only +test accounts are eligible for resync on Djust preprod
            if (!$isTestEmail) {
                ++$notInLocal;
                continue;
            }

            if ($effectiveTestPassword === '') {
                $io->error('DJUST_TEST_ACCOUNTS_PASSWORD is not configured. It is required to update +test accounts.');

                return Command::FAILURE;
            }

            ++$attempted;

            $update = [
                'accountId' => $accountId,
                'djustCustomerUserId' => $djustUser['id'],
                'djustCustomerAccountId' => $djustUser['customerAccountId'] ?? null,
                'djustUsername' => $djustUser['email'],
                'djustPassword' => $effectiveTestPassword,
            ];

            if ($dryRun) {
                $io->writeln(\sprintf(
                    '  [DRY-RUN] Would update account %s (%s → lookup:%s) → user_id=%s, account_id=%s, djust_username=%s, djust_password=***',
                    $accountId,
                    $djustUser['email'],
                    $lookupEmail,
                    $djustUser['id'],
                    $djustUser['customerAccountId'] ?? 'null',
                    $djustUser['email'],
                ));
            } else {
                $pendingUpdates[] = $update;
            }
        }

        $updated = 0;
        $errors = 0;
        if (!$dryRun && !empty($pendingUpdates)) {
            try {
                $this->resyncService->updateAccountDjustIdsBatch($pendingUpdates);
                $updated = \count($pendingUpdates);
            } catch (\Throwable $e) {
                $errors = \count($pendingUpdates);
                $this->logger->error('[ResyncDjustCredentials] Batch DB update failed', [
                    'error' => $e->getMessage(),
                ]);
                $io->error('Batch DB update failed: '.$e->getMessage());
            }
        }

        return $this->displayResult($attempted, $updated, $notInLocal, $errors, \count($djustUsers), $io);
    }

    private function displayResult(int $attempted, int $updated, int $notInLocal, int $errors, int $totalDjust, SymfonyStyle $io): int
    {
        $io->newLine();
        $io->info(\sprintf(
            'Djust preprod users: %d | Matched in local DB: %d | Updated: %d | Not in local DB: %d | Error(s): %d',
            $totalDjust,
            $attempted,
            $updated,
            $notInLocal,
            $errors,
        ));

        if ($errors > 0) {
            $io->error(\sprintf('Resync completed with %d error(s). Check logs for details.', $errors));

            return Command::FAILURE;
        }

        $io->success(\sprintf(
            'Resync completed: %d account(s) updated with preprod Djust IDs.',
            $updated,
        ));
        $io->comment((new \DateTime('now'))->format('Y-m-d H:i:s').': process finished');

        return Command::SUCCESS;
    }
}
