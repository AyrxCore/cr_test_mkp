<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Ajout du champ created_at à la table partner_store pour tracer la date d'import.
 */
final class Version20251001140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute le champ created_at à partner_store pour identifier quand un store a été importé';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE partner_store ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('COMMENT ON COLUMN partner_store.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE partner_store DROP created_at');
    }
}
