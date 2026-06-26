<?php

declare(strict_types=1);

namespace App\Command\_Exclude;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'channel:clean-obsolete-options',
    description: 'Supprime les options de channel obsolètes qui ne sont plus utilisées',
)]
class CleanObsoleteChannelOptionsCommand extends Command
{
    public function __construct(
        private readonly Connection $connection,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'option-names',
            InputArgument::IS_ARRAY | InputArgument::REQUIRED,
            'Noms des options à supprimer'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string[] $optionNames */
        $optionNames = $input->getArgument('option-names');

        $io->title('Nettoyage des options de channel obsolètes');
        $io->info('Options à supprimer : ' . implode(', ', $optionNames));

        $totalDeleted = 0;

        foreach ($optionNames as $optionName) {
            // Compter les options avec SQL direct
            $count = (int) $this->connection->fetchOne(
                'SELECT COUNT(*) FROM channel_option WHERE name = :name',
                ['name' => $optionName]
            );

            if ($count === 0) {
                $io->text("✓ Aucune option trouvée pour : {$optionName}");
                continue;
            }

            $io->text("⚠ Trouvé {$count} option(s) pour : {$optionName}");

            // Récupérer les channels concernés pour le log
            $channels = $this->connection->fetchAllAssociative(
                'SELECT DISTINCT c.code
                 FROM channel_option co
                 JOIN channel c ON co.channel_id = c.id
                 WHERE co.name = :name
                 ORDER BY c.code',
                ['name' => $optionName]
            );

            foreach ($channels as $channel) {
                $io->text("  - Suppression pour le channel : {$channel['code']}");
            }

            // Supprimer avec SQL direct
            $deletedForThisOption = $this->connection->executeStatement(
                'DELETE FROM channel_option WHERE name = :name',
                ['name' => $optionName]
            );

            $totalDeleted += $deletedForThisOption;
            $io->text("  ✓ {$deletedForThisOption} option(s) supprimée(s) et commitée(s) en base");
        }

        if ($totalDeleted > 0) {
            $io->success("✅ Total : {$totalDeleted} option(s) obsolète(s) supprimée(s) avec succès !");
        } else {
            $io->success('✅ Aucune option obsolète à supprimer. Base de données déjà propre !');
        }

        return Command::SUCCESS;
    }
}
