<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231114131806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE channel_parameter (id UUID NOT NULL, channel_id UUID NOT NULL, logo VARCHAR(255) DEFAULT NULL, favicon VARCHAR(255) DEFAULT NULL, primary_color VARCHAR(7) NOT NULL, secondary_color VARCHAR(7) NOT NULL, text_color VARCHAR(7) NOT NULL, privacy_policy VARCHAR(255) DEFAULT NULL, general_terms_of_use VARCHAR(255) DEFAULT NULL, legal_terms VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, white_label BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_85EA3A2F72F5A1AA ON channel_parameter (channel_id)');
        $this->addSql('COMMENT ON COLUMN channel_parameter.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN channel_parameter.channel_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE channel_parameter ADD CONSTRAINT FK_85EA3A2F72F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE channel DROP logo');
        $this->addSql('ALTER TABLE channel DROP favicon');
        $this->addSql('ALTER TABLE channel DROP email');
        $this->addSql('ALTER TABLE channel DROP phone_number');
        $this->addSql('ALTER TABLE channel DROP privacy_policy');
        $this->addSql('ALTER TABLE channel DROP legal_terms');
        $this->addSql('ALTER TABLE channel DROP general_terms_of_use');
        $this->addSql('ALTER TABLE channel DROP primary_color');
        $this->addSql('ALTER TABLE channel DROP secondary_color');
        $this->addSql('ALTER TABLE channel DROP text_color');
        $this->addSql('ALTER INDEX idx_7da0694e9b6b5fba RENAME TO IDX_41C40B0E9B6B5FBA');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE channel_parameter DROP CONSTRAINT FK_85EA3A2F72F5A1AA');
        $this->addSql('DROP TABLE channel_parameter');
        $this->addSql('ALTER TABLE channel ADD logo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE channel ADD favicon VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE channel ADD email VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE channel ADD phone_number VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE channel ADD privacy_policy VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE channel ADD legal_terms VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE channel ADD general_terms_of_use VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE channel ADD primary_color VARCHAR(7) NOT NULL');
        $this->addSql('ALTER TABLE channel ADD secondary_color VARCHAR(7) NOT NULL');
        $this->addSql('ALTER TABLE channel ADD text_color VARCHAR(7) NOT NULL');
        $this->addSql('ALTER INDEX idx_41c40b0e9b6b5fba RENAME TO idx_7da0694e9b6b5fba');
    }
}
