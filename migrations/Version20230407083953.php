<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230407083953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favorite (id UUID NOT NULL, account_id UUID NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, public BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_68C58ED99B6B5FBA ON favorite (account_id)');
        $this->addSql('CREATE UNIQUE INDEX name_account_unique_idx ON favorite (name, account_id)');
        $this->addSql('COMMENT ON COLUMN favorite.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN favorite.account_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN favorite.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN favorite.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE favorite_uppler_product (favorite_id UUID NOT NULL, uppler_product_id UUID NOT NULL, PRIMARY KEY(favorite_id, uppler_product_id))');
        $this->addSql('CREATE INDEX IDX_5CD7BAECAA17481D ON favorite_uppler_product (favorite_id)');
        $this->addSql('CREATE INDEX IDX_5CD7BAECB002F51B ON favorite_uppler_product (uppler_product_id)');
        $this->addSql('COMMENT ON COLUMN favorite_uppler_product.favorite_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN favorite_uppler_product.uppler_product_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE uppler_product (id UUID NOT NULL, uppler_product_id INT NOT NULL, uppler_variant_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN uppler_product.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE favorite ADD CONSTRAINT FK_68C58ED99B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE favorite_uppler_product ADD CONSTRAINT FK_5CD7BAECAA17481D FOREIGN KEY (favorite_id) REFERENCES favorite (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE favorite_uppler_product ADD CONSTRAINT FK_5CD7BAECB002F51B FOREIGN KEY (uppler_product_id) REFERENCES uppler_product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE favorite DROP CONSTRAINT FK_68C58ED99B6B5FBA');
        $this->addSql('ALTER TABLE favorite_uppler_product DROP CONSTRAINT FK_5CD7BAECAA17481D');
        $this->addSql('ALTER TABLE favorite_uppler_product DROP CONSTRAINT FK_5CD7BAECB002F51B');
        $this->addSql('DROP TABLE favorite');
        $this->addSql('DROP TABLE favorite_uppler_product');
        $this->addSql('DROP TABLE uppler_product');
    }
}
