<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260727151647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'MKP-1557: add seller_order_id column to cart_savings (Djust orderLogistic id, distinct from the parent order id)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cart_savings ADD seller_order_id VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cart_savings DROP seller_order_id');
    }
}
