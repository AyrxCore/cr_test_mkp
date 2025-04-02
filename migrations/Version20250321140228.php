<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250321140228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE partner (id UUID NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id), uppler_id int NOT NULL)');
        $this->addSql('COMMENT ON COLUMN partner.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN partner.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN partner.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE partner_store (id UUID NOT NULL, partner_id UUID NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, latitude VARCHAR(255) NOT NULL, longitude VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN partner_store.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN partner_store.partner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE INDEX IDX_2205EF989393F8FE ON partner_store (partner_id)');
        $this->addSql('ALTER TABLE partner_store ADD CONSTRAINT FK_2205EF989393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE partner_store_id_seq CASCADE');
        $this->addSql('ALTER TABLE partner_store DROP CONSTRAINT FK_2205EF989393F8FE');
        $this->addSql('DROP TABLE partner');
        $this->addSql('DROP TABLE partner_store');
    }
}
