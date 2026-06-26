<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260210132941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add djust_id to partner and create index on it, rename index on partner_store';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IF EXISTS IDX_partner_uppler_id');
        $this->addSql('DROP INDEX IF EXISTS IDX_partner_store_partner_id');
        $this->addSql('ALTER TABLE partner ADD djust_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_partner_uppler_id ON partner (uppler_id)');
        $this->addSql('CREATE INDEX idx_partner_djust_id ON partner (djust_id)');
        $this->addSql('ALTER INDEX idx_2205ef989393f8fe RENAME TO idx_partner_store_partner_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_partner_uppler_id');
        $this->addSql('DROP INDEX idx_partner_djust_id');
        $this->addSql('ALTER TABLE partner DROP djust_id');
        $this->addSql('ALTER INDEX idx_partner_store_partner_id RENAME TO idx_2205ef989393f8fe');
    }
}
