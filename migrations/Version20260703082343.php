<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260703082343 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE favorite DROP CONSTRAINT fk_68c58ed99b6b5fba');
        $this->addSql('ALTER TABLE favorite_product DROP CONSTRAINT fk_8e1eaac3aa17481d');
        $this->addSql('ALTER TABLE saved_cart DROP CONSTRAINT fk_59c7aa29b6b5fba');
        $this->addSql('ALTER TABLE saved_cart_product DROP CONSTRAINT fk_a536d30c90d40228');
        $this->addSql('DROP TABLE favorite');
        $this->addSql('DROP TABLE favorite_product');
        $this->addSql('DROP TABLE saved_cart');
        $this->addSql('DROP TABLE saved_cart_product');
        $this->addSql('DROP INDEX uniq_7d3656a4e7a1254a');
        $this->addSql('ALTER TABLE account DROP uppler_user_id');
        $this->addSql('ALTER TABLE account DROP uppler_sub_account_id');
        $this->addSql('ALTER TABLE account DROP uppler_company_id');
        $this->addSql('ALTER TABLE account DROP uppler_username');
        $this->addSql('ALTER TABLE account DROP uppler_password');
        $this->addSql('ALTER TABLE account DROP uppler_client_id');
        $this->addSql('ALTER TABLE account DROP uppler_client_secret');
        $this->addSql('DROP INDEX idx_partner_uppler_id');
        $this->addSql('ALTER TABLE partner DROP uppler_id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE favorite (id UUID NOT NULL, account_id UUID NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, public BOOLEAN NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX idx_68c58ed99b6b5fba ON favorite (account_id)');
        $this->addSql('CREATE UNIQUE INDEX name_account_unique_idx ON favorite (name, account_id)');
        $this->addSql('COMMENT ON COLUMN favorite.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN favorite.account_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN favorite.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN favorite.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE favorite_product (id UUID NOT NULL, favorite_id UUID NOT NULL, uppler_product_id INT NOT NULL, uppler_variant_id INT NOT NULL, uppler_product_name VARCHAR(255) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX idx_8e1eaac3aa17481d ON favorite_product (favorite_id)');
        $this->addSql('COMMENT ON COLUMN favorite_product.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN favorite_product.favorite_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE saved_cart (id UUID NOT NULL, account_id UUID NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX saved_cart_name_account_unique_idx ON saved_cart (name, account_id)');
        $this->addSql('CREATE INDEX idx_59c7aa29b6b5fba ON saved_cart (account_id)');
        $this->addSql('COMMENT ON COLUMN saved_cart.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN saved_cart.account_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN saved_cart.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN saved_cart.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE saved_cart_product (id UUID NOT NULL, saved_cart_id UUID NOT NULL, uppler_product_id INT NOT NULL, uppler_variant_id INT NOT NULL, uppler_product_name VARCHAR(255) NOT NULL, quantity INT NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX idx_a536d30c90d40228 ON saved_cart_product (saved_cart_id)');
        $this->addSql('COMMENT ON COLUMN saved_cart_product.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN saved_cart_product.saved_cart_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE favorite ADD CONSTRAINT fk_68c58ed99b6b5fba FOREIGN KEY (account_id) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE favorite_product ADD CONSTRAINT fk_8e1eaac3aa17481d FOREIGN KEY (favorite_id) REFERENCES favorite (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE saved_cart ADD CONSTRAINT fk_59c7aa29b6b5fba FOREIGN KEY (account_id) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE saved_cart_product ADD CONSTRAINT fk_a536d30c90d40228 FOREIGN KEY (saved_cart_id) REFERENCES saved_cart (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE account ADD uppler_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE account ADD uppler_sub_account_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE account ADD uppler_company_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE account ADD uppler_username VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE account ADD uppler_password VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE account ADD uppler_client_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE account ADD uppler_client_secret VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_7d3656a4e7a1254a ON account (contact_id)');
        $this->addSql('ALTER TABLE partner ADD uppler_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX idx_partner_uppler_id ON partner (uppler_id)');
    }
}
