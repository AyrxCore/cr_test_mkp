<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260827100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'MKP-1529: add partner_id column to cart_savings (SUGAR/NEO id of the seller for PowerBI reporting)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cart_savings ADD partner_id VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cart_savings DROP partner_id');
    }
}

