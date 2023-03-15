<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230315113215 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart_savings (id UUID NOT NULL, account_id UUID NOT NULL, cart_id INT NOT NULL, order_id INT NOT NULL, seller_id INT NOT NULL, amount INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E9DB971A9B6B5FBA ON cart_savings (account_id)');
        $this->addSql('COMMENT ON COLUMN cart_savings.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN cart_savings.account_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN cart_savings.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE cart_savings ADD CONSTRAINT FK_E9DB971A9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE cart_savings DROP CONSTRAINT FK_E9DB971A9B6B5FBA');
        $this->addSql('DROP TABLE cart_savings');
    }
}
