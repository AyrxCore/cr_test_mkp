<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230731142815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adherent_channel (id UUID NOT NULL, channel_id UUID NOT NULL, adherent_id UUID NOT NULL, logo VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2788BCC972F5A1AA ON adherent_channel (channel_id)');
        $this->addSql('CREATE INDEX IDX_2788BCC925F06C53 ON adherent_channel (adherent_id)');
        $this->addSql('CREATE UNIQUE INDEX channel_adherent_unique_idx ON adherent_channel (adherent_id, channel_id)');
        $this->addSql('COMMENT ON COLUMN adherent_channel.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN adherent_channel.channel_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN adherent_channel.adherent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE adherent_channel ADD CONSTRAINT FK_2788BCC972F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE adherent_channel ADD CONSTRAINT FK_2788BCC925F06C53 FOREIGN KEY (adherent_id) REFERENCES adherent (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adherent_channel DROP CONSTRAINT FK_2788BCC972F5A1AA');
        $this->addSql('ALTER TABLE adherent_channel DROP CONSTRAINT FK_2788BCC925F06C53');
        $this->addSql('DROP TABLE adherent_channel');
    }
}
