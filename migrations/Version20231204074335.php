<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231204074335 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE channel_option (id UUID NOT NULL, channel_id UUID NOT NULL, name VARCHAR(255) NOT NULL, value TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A4E53ACE72F5A1AA ON channel_option (channel_id)');
        $this->addSql('COMMENT ON COLUMN channel_option.channel_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN channel_option.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE channel_option ADD CONSTRAINT FK_A4E53ACE72F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE channel_option DROP CONSTRAINT FK_A4E53ACE72F5A1AA');
        $this->addSql('DROP TABLE channel_option');
    }
}
