<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230705143913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE user_info_update_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE user_info_update_request (id INT NOT NULL, _user_id UUID DEFAULT NULL, account_id UUID DEFAULT NULL, attribute VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, old_value VARCHAR(255) NOT NULL, email_changing_token VARCHAR(255) DEFAULT NULL, email_changing_requested_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_iso BOOLEAN NOT NULL, iso_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AF14B990D32632E8 ON user_info_update_request (_user_id)');
        $this->addSql('CREATE INDEX IDX_AF14B9909B6B5FBA ON user_info_update_request (account_id)');
        $this->addSql('COMMENT ON COLUMN user_info_update_request._user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_info_update_request.account_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_info_update_request.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_info_update_request.iso_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user_info_update_request ADD CONSTRAINT FK_AF14B990D32632E8 FOREIGN KEY (_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_info_update_request ADD CONSTRAINT FK_AF14B9909B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE account ADD phone VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE user_info_update_request_id_seq CASCADE');
        $this->addSql('ALTER TABLE user_info_update_request DROP CONSTRAINT FK_AF14B990D32632E8');
        $this->addSql('ALTER TABLE user_info_update_request DROP CONSTRAINT FK_AF14B9909B6B5FBA');
        $this->addSql('DROP TABLE user_info_update_request');
        $this->addSql('ALTER TABLE account DROP phone');
    }
}
