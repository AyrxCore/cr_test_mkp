<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260401000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove djust_id from partner (id is now used directly as Djust identifier)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX IF EXISTS idx_partner_djust_id');
        $this->addSql('ALTER TABLE partner DROP COLUMN IF EXISTS djust_id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE partner ADD djust_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_partner_djust_id ON partner (djust_id)');
    }
}
