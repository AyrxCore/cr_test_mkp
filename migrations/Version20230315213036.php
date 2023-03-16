<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230315213036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE log_accord_statut_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE log_accord_statut_request (id INT NOT NULL, account_id UUID NOT NULL, accord_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_649483E29B6B5FBA ON log_accord_statut_request (account_id)');
        $this->addSql('COMMENT ON COLUMN log_accord_statut_request.account_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN log_accord_statut_request.accord_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN log_accord_statut_request.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE log_accord_statut_request ADD CONSTRAINT FK_649483E29B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE log_accord_statut_request_id_seq CASCADE');
        $this->addSql('ALTER TABLE log_accord_statut_request DROP CONSTRAINT FK_649483E29B6B5FBA');
        $this->addSql('DROP TABLE log_accord_statut_request');
    }
}
