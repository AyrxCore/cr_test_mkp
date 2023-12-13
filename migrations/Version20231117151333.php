<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231117151333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adherent_channel DROP CONSTRAINT fk_2788bcc972f5a1aa');
        $this->addSql('ALTER TABLE adherent_channel DROP CONSTRAINT fk_2788bcc925f06c53');
        $this->addSql('DROP TABLE adherent_channel');
        $this->addSql('ALTER TABLE adherent ADD channel_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE adherent ADD logo VARCHAR(255) DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN adherent.channel_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE adherent ADD CONSTRAINT FK_90D3F06072F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_90D3F06072F5A1AA ON adherent (channel_id)');
        $this->addSql('ALTER TABLE user_info_update_request ALTER old_value DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE adherent_channel (id UUID NOT NULL, channel_id UUID DEFAULT NULL, adherent_id UUID DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX channel_adherent_unique_idx ON adherent_channel (adherent_id, channel_id)');
        $this->addSql('CREATE INDEX idx_2788bcc925f06c53 ON adherent_channel (adherent_id)');
        $this->addSql('CREATE INDEX idx_2788bcc972f5a1aa ON adherent_channel (channel_id)');
        $this->addSql('COMMENT ON COLUMN adherent_channel.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN adherent_channel.channel_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN adherent_channel.adherent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE adherent_channel ADD CONSTRAINT fk_2788bcc972f5a1aa FOREIGN KEY (channel_id) REFERENCES channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE adherent_channel ADD CONSTRAINT fk_2788bcc925f06c53 FOREIGN KEY (adherent_id) REFERENCES adherent (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE adherent DROP CONSTRAINT FK_90D3F06072F5A1AA');
        $this->addSql('DROP INDEX IDX_90D3F06072F5A1AA');
        $this->addSql('ALTER TABLE adherent DROP channel_id');
        $this->addSql('ALTER TABLE adherent DROP logo');
        $this->addSql('ALTER TABLE user_info_update_request ALTER old_value SET NOT NULL');
    }
}
