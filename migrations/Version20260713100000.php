<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260713100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'MKP-1520 — Add first_connection_at column and index to account table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE account ADD first_connection_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql("COMMENT ON COLUMN account.first_connection_at IS '(DC2Type:datetime_immutable)'");
        $this->addSql('CREATE INDEX idx_account_first_connection_at ON account (first_connection_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_account_first_connection_at');
        $this->addSql('ALTER TABLE account DROP COLUMN first_connection_at');
    }
}
