<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260723091148 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add updated_at column to adherent table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE adherent ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('UPDATE adherent SET updated_at = NOW() WHERE updated_at IS NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE adherent DROP updated_at');
    }
}
