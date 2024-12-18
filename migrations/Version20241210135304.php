<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241210135304 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_savings ADD order_total INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE cart_savings ADD items_total_before_savings INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE cart_savings ADD items_total INT NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_savings DROP order_total');
        $this->addSql('ALTER TABLE cart_savings DROP items_total_before_savings');
        $this->addSql('ALTER TABLE cart_savings DROP items_total');
    }
}
