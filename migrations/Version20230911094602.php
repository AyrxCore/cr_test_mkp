<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230911094602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE log_account_connection_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE log_account_connection (id INT NOT NULL, account_id UUID NOT NULL, connected_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7DA0694E9B6B5FBA ON log_account_connection (account_id)');
        $this->addSql('COMMENT ON COLUMN log_account_connection.account_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN log_account_connection.connected_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE log_account_connection ADD CONSTRAINT FK_7DA0694E9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE log_account_connection_id_seq CASCADE');
        $this->addSql('ALTER TABLE log_account_connection DROP CONSTRAINT FK_7DA0694E9B6B5FBA');
        $this->addSql('DROP TABLE log_account_connection');
    }
}
