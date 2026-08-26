<?php

declare(strict_types=1);

namespace App\Command\_Exclude;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'account:backfill-first-connection-at',
    description: 'MKP-1520 — One-shot: backfill first_connection_at on account from log_account_connection (usurpations excluded)',
)]
class BackfillFirstConnectionAtCommand extends Command
{
    public function __construct(private readonly Connection $connection)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $updated = $this->connection->executeStatement(
            'UPDATE account a
             SET first_connection_at = sub.first_connected_at
             FROM (
                 SELECT account_id, MIN(connected_at) AS first_connected_at
                 FROM log_account_connection
                 GROUP BY account_id
             ) sub
             WHERE a.id = sub.account_id
               AND a.first_connection_at IS NULL'
        );

        $io->success(\sprintf('%d compte(s) mis à jour avec first_connection_at.', $updated));

        return Command::SUCCESS;
    }
}
