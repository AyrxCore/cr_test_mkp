<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241029092508 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE adherent_tarif_showcase_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE adherent_tarif_showcase (id INT NOT NULL, adherent_id UUID NOT NULL, accord_id UUID NOT NULL, tarif_id UUID NOT NULL, contact_requested BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9DD3E0C225F06C53 ON adherent_tarif_showcase (adherent_id)');
        $this->addSql('COMMENT ON COLUMN adherent_tarif_showcase.adherent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN adherent_tarif_showcase.accord_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN adherent_tarif_showcase.tarif_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE adherent_tarif_showcase ADD CONSTRAINT FK_9DD3E0C225F06C53 FOREIGN KEY (adherent_id) REFERENCES adherent (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE adherent_tarif_showcase_id_seq CASCADE');
        $this->addSql('ALTER TABLE adherent_tarif_showcase DROP CONSTRAINT FK_9DD3E0C225F06C53');
        $this->addSql('DROP TABLE adherent_tarif_showcase');
    }
}
