<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260713072506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add updated_at column to channel and log_account_connection tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE channel ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE log_account_connection ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE channel DROP updated_at');
        $this->addSql('ALTER TABLE log_account_connection DROP updated_at');
    }
}
