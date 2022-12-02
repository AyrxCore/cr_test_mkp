<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221202143831 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE cron_job_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE cron_report_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE refresh_tokens_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE account (id UUID NOT NULL, _user_id UUID NOT NULL, uppler_user_id INT NOT NULL, uppler_sub_account_id INT NOT NULL, uppler_company_id INT NOT NULL, uppler_username VARCHAR(255) NOT NULL, uppler_password VARCHAR(255) NOT NULL, last_connexion TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uppler_client_id VARCHAR(255) DEFAULT NULL, uppler_client_secret VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7D3656A4D32632E8 ON account (_user_id)');
        $this->addSql('COMMENT ON COLUMN account.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN account._user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE cron_job (id INT NOT NULL, name VARCHAR(191) NOT NULL, command VARCHAR(1024) NOT NULL, schedule VARCHAR(191) NOT NULL, description VARCHAR(191) NOT NULL, enabled BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX un_name ON cron_job (name)');
        $this->addSql('CREATE TABLE cron_report (id INT NOT NULL, job_id INT DEFAULT NULL, run_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, run_time DOUBLE PRECISION NOT NULL, exit_code INT NOT NULL, output TEXT NOT NULL, error TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B6C6A7F5BE04EA9 ON cron_report (job_id)');
        $this->addSql('CREATE TABLE refresh_tokens (id INT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9BACE7E1C74F2195 ON refresh_tokens (refresh_token)');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_enabled BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A4D32632E8 FOREIGN KEY (_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cron_report ADD CONSTRAINT FK_B6C6A7F5BE04EA9 FOREIGN KEY (job_id) REFERENCES cron_job (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE cron_job_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE cron_report_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE refresh_tokens_id_seq CASCADE');
        $this->addSql('ALTER TABLE account DROP CONSTRAINT FK_7D3656A4D32632E8');
        $this->addSql('ALTER TABLE cron_report DROP CONSTRAINT FK_B6C6A7F5BE04EA9');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE cron_job');
        $this->addSql('DROP TABLE cron_report');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE "user"');
    }
}
