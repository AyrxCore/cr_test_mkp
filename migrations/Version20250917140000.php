<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Optimisation des performances pour les requêtes de carte
 * Garde uniquement les index réellement utilisés par les requêtes actuelles.
 */
final class Version20250917140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute des index utiles: partner.uppler_id et partner_store.partner_id';
    }

    public function up(Schema $schema): void
    {
        // Index sur uppler_id pour Partner (filtrage via IN (:upplerIds))
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_partner_uppler_id ON partner (uppler_id)');

        // Index simple sur partner_store.partner_id pour accélérer les JOIN partner -> partner_store
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_partner_store_partner_id ON partner_store (partner_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IF EXISTS IDX_partner_uppler_id');
        $this->addSql('DROP INDEX IF EXISTS IDX_partner_store_partner_id');
    }
}
