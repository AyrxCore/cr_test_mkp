<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250611082101 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE accord (id UUID NOT NULL, partner_id UUID NOT NULL, name VARCHAR(255) NOT NULL, logo VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, has_store BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_91361A049393F8FE ON accord (partner_id)');
        $this->addSql('COMMENT ON COLUMN accord.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN accord.partner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN accord.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN accord.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE accord_partner_store (accord_id UUID NOT NULL, partner_store_id UUID NOT NULL, PRIMARY KEY(accord_id, partner_store_id))');
        $this->addSql('CREATE INDEX IDX_374246AB1EDF023F ON accord_partner_store (accord_id)');
        $this->addSql('CREATE INDEX IDX_374246ABD3532EAB ON accord_partner_store (partner_store_id)');
        $this->addSql('COMMENT ON COLUMN accord_partner_store.accord_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN accord_partner_store.partner_store_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE accord ADD CONSTRAINT FK_91361A049393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE accord_partner_store ADD CONSTRAINT FK_374246AB1EDF023F FOREIGN KEY (accord_id) REFERENCES accord (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE accord_partner_store ADD CONSTRAINT FK_374246ABD3532EAB FOREIGN KEY (partner_store_id) REFERENCES partner_store (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE accord DROP CONSTRAINT FK_91361A049393F8FE');
        $this->addSql('ALTER TABLE accord_partner_store DROP CONSTRAINT FK_374246AB1EDF023F');
        $this->addSql('ALTER TABLE accord_partner_store DROP CONSTRAINT FK_374246ABD3532EAB');
        $this->addSql('DROP TABLE accord');
        $this->addSql('DROP TABLE accord_partner_store');
    }
}
