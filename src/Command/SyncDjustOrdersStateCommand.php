<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Djust\DjustOrdersSyncService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:sync-djust-orders-state',
    description: 'Sync Djust order states into CartSavings for analytics reporting.',
)]
class SyncDjustOrdersStateCommand extends Command
{
    public function __construct(
        private readonly DjustOrdersSyncService $djustOrdersSyncService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $result = $this->djustOrdersSyncService->sync();

        $io->success(\sprintf(
            'Djust orders sync completed. processed=%d updated=%d skipped=%d failed=%d',
            $result['processed'],
            $result['updated'],
            $result['skipped'],
            $result['failed'],
        ));

        return $result['failed'] === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}
