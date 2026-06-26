<?php

declare(strict_types=1);

namespace App\Command\Djust;

use App\Repository\AccountRepository;
use App\Service\CredentialEncryptionService;
use App\Service\Djust\DjustAccountApiService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'djust:delete-cart',
    description: 'Delete the open cart(s) of a Djust user by their email address',
)]
class DeleteDjustCartCommand extends Command
{
    public function __construct(
        private readonly DjustAccountApiService $accountApi,
        private readonly AccountRepository $accountRepository,
        private readonly CredentialEncryptionService $credentialEncryptionService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email address of the Djust user whose cart(s) should be deleted')
            ->setHelp('This command finds the open cart(s) of a Djust user by email and deletes them via the Djust operator API.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');

        // Step 1 — resolve account from local DB
        $io->section(\sprintf('Looking up account: %s', $email));

        $account = $this->accountRepository->findOneBy(['djustUsername' => $email]);

        if ($account === null) {
            $io->error(\sprintf('No Djust account found for email: %s', $email));

            return Command::FAILURE;
        }

        $customerAccountId = $account->getDjustCustomerAccountId();

        if ($customerAccountId === null) {
            $io->error(\sprintf('Account found (djustUsername: %s) but djustCustomerAccountId is null.', $email));

            return Command::FAILURE;
        }

        $storeViewCode = $account->getAdherent()?->getChannel()?->getCode();

        if ($storeViewCode === null) {
            $io->error('Cannot resolve channel code (dj-store-view) for this account.');

            return Command::FAILURE;
        }

        $io->writeln(\sprintf('  Customer Account ID : <info>%s</info>', $customerAccountId));
        $io->writeln(\sprintf('  Channel (store-view): <info>%s</info>', $storeViewCode));

        // Step 2 — authenticate as the account user
        if ($account->getDjustPassword() === null) {
            $io->error('Djust password is not set for this account.');

            return Command::FAILURE;
        }

        try {
            $plaintextPassword = $this->credentialEncryptionService->decrypt($account->getDjustPassword());
            $accountToken = $this->accountApi->getAccessToken($account->getDjustUsername(), $plaintextPassword);
        } catch (\Throwable $e) {
            $io->error('Failed to authenticate as Djust account user: '.$e->getMessage());

            return Command::FAILURE;
        }

        // Step 3 — retrieve open carts
        try {
            $carts = $this->accountApi->getOpenCarts($customerAccountId, $accountToken, $storeViewCode);
        } catch (\Throwable $e) {
            $io->error('Failed to retrieve carts: '.$e->getMessage());

            return Command::FAILURE;
        }

        if (empty($carts)) {
            $io->success(\sprintf('No open cart found for user "%s".', $email));

            return Command::SUCCESS;
        }

        $io->section(\sprintf('%d open cart(s) found', \count($carts)));

        $rows = [];
        foreach ($carts as $cart) {
            $rows[] = [
                $cart['id'] ?? 'N/A',
                $cart['reference'] ?? 'N/A',
                $cart['productCount'] ?? 0,
                $cart['createdAt'] ?? 'N/A',
            ];
        }

        $io->table(['Cart ID (business)', 'Reference (for delete)', 'Product count', 'Created at'], $rows);

        // Step 4 — confirm before deletion
        if (!$io->confirm(\sprintf('Delete %d cart(s) for user "%s"? This action cannot be undone.', \count($carts), $email), false)) {
            $io->note('Operation cancelled.');

            return Command::SUCCESS;
        }

        // Step 5 — delete each cart
        $errors = [];
        foreach ($carts as $cart) {
            $cartReference = $cart['reference'] ?? null;

            if ($cartReference === null) {
                $io->warning(\sprintf('Skipped cart %s: no reference field found.', $cart['id'] ?? 'N/A'));
                continue;
            }

            try {
                $this->accountApi->deleteCart($cartReference, $customerAccountId, $accountToken, $storeViewCode);
                $io->writeln(\sprintf('  Deleted cart <info>%s</info> (ref: %s)', $cart['id'] ?? 'N/A', $cartReference));
            } catch (\Throwable $e) {
                $errors[] = \sprintf('Cart %s (ref: %s): %s', $cart['id'] ?? 'N/A', $cartReference, $e->getMessage());
            }
        }

        if (!empty($errors)) {
            $io->error(\array_merge(['Some carts could not be deleted:'], $errors));

            return Command::FAILURE;
        }

        $io->success(\sprintf('Successfully deleted %d cart(s) for user "%s".', \count($carts), $email));

        return Command::SUCCESS;
    }
}
