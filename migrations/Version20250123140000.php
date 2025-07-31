<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Make partner_store.phone field nullable.
 */
final class Version20250123140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make partner_store.phone field nullable to distinguish between no phone and invalid phone';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partner_store ALTER phone DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('UPDATE partner_store SET phone = \'\' WHERE phone IS NULL');
        $this->addSql('ALTER TABLE partner_store ALTER phone SET NOT NULL');
    }
}
