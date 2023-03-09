<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230309115411 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE account_accord_cadre_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE accord_statut_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE accord_statut (id INT NOT NULL, adherent_id UUID NOT NULL, accord_id UUID NOT NULL, status VARCHAR(255) NOT NULL, accord_statut_request_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_61C8D93525F06C53 ON accord_statut (adherent_id)');
        $this->addSql('COMMENT ON COLUMN accord_statut.adherent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN accord_statut.accord_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE adherent (id UUID NOT NULL, name VARCHAR(255) NOT NULL, reducce_code VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN adherent.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE accord_statut ADD CONSTRAINT FK_61C8D93525F06C53 FOREIGN KEY (adherent_id) REFERENCES adherent (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE account_accord_cadre');
        $this->addSql('ALTER TABLE account ADD adherent_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN account.adherent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A425F06C53 FOREIGN KEY (adherent_id) REFERENCES adherent (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_7D3656A425F06C53 ON account (adherent_id)');
        $this->addSql('ALTER TABLE "user" ADD acces_market_place BOOLEAN DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE account DROP CONSTRAINT FK_7D3656A425F06C53');
        $this->addSql('DROP SEQUENCE accord_statut_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE account_accord_cadre_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE account_accord_cadre (id UUID NOT NULL, account_id VARCHAR(255) NOT NULL, accord_cadre_id INT NOT NULL, status VARCHAR(15) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE accord_statut DROP CONSTRAINT FK_61C8D93525F06C53');
        $this->addSql('DROP TABLE accord_statut');
        $this->addSql('DROP TABLE adherent');
        $this->addSql('DROP INDEX IDX_7D3656A425F06C53');
        $this->addSql('ALTER TABLE account DROP adherent_id');
        $this->addSql('ALTER TABLE "user" DROP acces_market_place');
    }
}
