<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240129080548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adherent ADD parent_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN adherent.parent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE adherent ADD CONSTRAINT FK_90D3F060727ACA70 FOREIGN KEY (parent_id) REFERENCES adherent (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_90D3F060727ACA70 ON adherent (parent_id)');
        $this->addSql('ALTER TABLE adherent ADD reducce_service_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE adherent ADD reducce_url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adherent DROP CONSTRAINT FK_90D3F060727ACA70');
        $this->addSql('DROP INDEX UNIQ_90D3F060727ACA70');
        $this->addSql('ALTER TABLE adherent DROP parent_id');
        $this->addSql('ALTER TABLE adherent DROP reducce_service_name');
        $this->addSql('ALTER TABLE adherent DROP reducce_url');
    }
}
