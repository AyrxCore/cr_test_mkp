<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230919151655 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account ADD service_fonction VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE adherent ADD street VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE adherent ADD city VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE adherent ADD postalcode VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE adherent ADD country VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE adherent ADD activite_ape VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adherent DROP street');
        $this->addSql('ALTER TABLE adherent DROP city');
        $this->addSql('ALTER TABLE adherent DROP postalcode');
        $this->addSql('ALTER TABLE adherent DROP country');
        $this->addSql('ALTER TABLE adherent DROP activite_ape');
        $this->addSql('ALTER TABLE account DROP service_fonction');
    }
}
