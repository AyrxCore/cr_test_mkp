<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230815090417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'DROP "sales_conditions" column, and make "privacy_policy", "legal_terms" and "general_terms_of_use" columns nullable in "channel" table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adherent_channel ALTER channel_id DROP NOT NULL');
        $this->addSql('ALTER TABLE adherent_channel ALTER adherent_id DROP NOT NULL');
        $this->addSql('ALTER TABLE channel DROP sales_conditions');
        $this->addSql('ALTER TABLE channel ALTER privacy_policy DROP NOT NULL');
        $this->addSql('ALTER TABLE channel ALTER legal_terms DROP NOT NULL');
        $this->addSql('ALTER TABLE channel ALTER general_terms_of_use DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE channel ADD sales_conditions VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE adherent_channel ALTER channel_id SET NOT NULL');
        $this->addSql('ALTER TABLE adherent_channel ALTER adherent_id SET NOT NULL');
        $this->addSql('ALTER TABLE channel ALTER privacy_policy SET NOT NULL');
        $this->addSql('ALTER TABLE channel ALTER legal_terms SET NOT NULL');
        $this->addSql('ALTER TABLE channel ALTER general_terms_of_use SET NOT NULL');
    }
}
