<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230628093223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename account._user_id column to account.user_id';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account DROP CONSTRAINT fk_7d3656a4d32632e8');
        $this->addSql('DROP INDEX idx_7d3656a4d32632e8');
        $this->addSql('ALTER TABLE account RENAME COLUMN _user_id TO user_id');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A4A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_7D3656A4A76ED395 ON account (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE account DROP CONSTRAINT FK_7D3656A4A76ED395');
        $this->addSql('DROP INDEX IDX_7D3656A4A76ED395');
        $this->addSql('ALTER TABLE account RENAME COLUMN user_id TO _user_id');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT fk_7d3656a4d32632e8 FOREIGN KEY (_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_7d3656a4d32632e8 ON account (_user_id)');
    }
}
