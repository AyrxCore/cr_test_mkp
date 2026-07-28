<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260728092508 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove unused updated_at column from log_account_connection table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE log_account_connection DROP COLUMN updated_at');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE log_account_connection ADD COLUMN updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }
}
