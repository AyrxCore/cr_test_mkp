<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230721142851 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create channel table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE channel (id UUID NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(20) NOT NULL, hostname VARCHAR(128) NOT NULL, logo VARCHAR(255) DEFAULT NULL, favicon VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, phone_number VARCHAR(255) NOT NULL, sales_conditions VARCHAR(255) NOT NULL, privacy_policy VARCHAR(255) NOT NULL, legal_terms VARCHAR(255) NOT NULL, general_terms_of_use VARCHAR(255) NOT NULL, primary_color VARCHAR(7) NOT NULL, secondary_color VARCHAR(7) NOT NULL, text_color VARCHAR(7) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A2F98E4777153098 ON channel (code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A2F98E47E551C011 ON channel (hostname)');
        $this->addSql('CREATE UNIQUE INDEX code_hostname_unique_idx ON channel (code, hostname)');
        $this->addSql('COMMENT ON COLUMN channel.id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE channel');
    }
}
