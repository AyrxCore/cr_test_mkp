<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260216090007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "Création de la table shipping_rule";
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE shipping_rule (id UUID NOT NULL, type VARCHAR(255) NOT NULL, rule JSONB NOT NULL, partner_id UUID NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_D7643ACE9393F8FE ON shipping_rule (partner_id)');
        $this->addSql('ALTER TABLE shipping_rule ADD CONSTRAINT FK_D7643ACE9393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id) NOT DEFERRABLE');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shipping_rule DROP CONSTRAINT FK_D7643ACE9393F8FE');
        $this->addSql('DROP TABLE shipping_rule');
    }
}
