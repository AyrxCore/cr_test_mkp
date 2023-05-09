<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230502152045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favorite_product (id UUID NOT NULL , favorite_id UUID NOT NULL, uppler_product_id INT NOT NULL, uppler_variant_id INT NOT NULL, uppler_product_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8E1EAAC3AA17481D ON favorite_product (favorite_id)');
        $this->addSql('INSERT INTO favorite_product (id, favorite_id, uppler_product_id, uppler_variant_id, uppler_product_name)
           SELECT gen_random_uuid(), fup.favorite_id, up.uppler_product_id, up.uppler_variant_id, up.name FROM uppler_product up
            INNER JOIN favorite_uppler_product fup on up.id = fup.uppler_product_id
            ');
        $this->addSql('COMMENT ON COLUMN favorite_product.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN favorite_product.favorite_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "saved_cart" (id UUID NOT NULL, account_id UUID NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_59C7AA29B6B5FBA ON "saved_cart" (account_id)');
        $this->addSql('CREATE UNIQUE INDEX saved_cart_name_account_unique_idx ON "saved_cart" (name, account_id)');
        $this->addSql('COMMENT ON COLUMN "saved_cart".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "saved_cart".account_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "saved_cart".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "saved_cart".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE saved_cart_product (id UUID NOT NULL, saved_cart_id UUID NOT NULL, uppler_product_id INT NOT NULL, uppler_variant_id INT NOT NULL, uppler_product_name VARCHAR(255) NOT NULL, quantity INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A536D30C90D40228 ON saved_cart_product (saved_cart_id)');
        $this->addSql('COMMENT ON COLUMN saved_cart_product.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN saved_cart_product.saved_cart_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE favorite_product ADD CONSTRAINT FK_8E1EAAC3AA17481D FOREIGN KEY (favorite_id) REFERENCES favorite (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "saved_cart" ADD CONSTRAINT FK_59C7AA29B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE saved_cart_product ADD CONSTRAINT FK_A536D30C90D40228 FOREIGN KEY (saved_cart_id) REFERENCES "saved_cart" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE favorite_uppler_product DROP CONSTRAINT fk_5cd7baecaa17481d');
        $this->addSql('ALTER TABLE favorite_uppler_product DROP CONSTRAINT fk_5cd7baecb002f51b');
        $this->addSql('DROP TABLE uppler_product');
        $this->addSql('DROP TABLE favorite_uppler_product');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE uppler_product (id UUID NOT NULL, uppler_product_id INT NOT NULL, uppler_variant_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN uppler_product.id IS \'(DC2Type:uuid)\'');
        $this->addSql('INSERT INTO uppler_product (id, uppler_product_id, uppler_variant_id, name)
           SELECT fp.id, fp.uppler_product_id, fp.uppler_variant_id, fp.uppler_product_name FROM favorite_product fp
            GROUP BY fp.uppler_product_id, fp.uppler_variant_id
            ');
        $this->addSql('CREATE TABLE favorite_uppler_product (favorite_id UUID NOT NULL, uppler_product_id UUID NOT NULL, PRIMARY KEY(favorite_id, uppler_product_id))');
        $this->addSql('INSERT INTO favorite_uppler_product (favorite_id, uppler_product_id) SELECT fp.favorite_id, fp.id FROM favorite_product fp');
        $this->addSql('CREATE INDEX idx_5cd7baecb002f51b ON favorite_uppler_product (uppler_product_id)');
        $this->addSql('CREATE INDEX idx_5cd7baecaa17481d ON favorite_uppler_product (favorite_id)');
        $this->addSql('COMMENT ON COLUMN favorite_uppler_product.favorite_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN favorite_uppler_product.uppler_product_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE favorite_uppler_product ADD CONSTRAINT fk_5cd7baecaa17481d FOREIGN KEY (favorite_id) REFERENCES favorite (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE favorite_uppler_product ADD CONSTRAINT fk_5cd7baecb002f51b FOREIGN KEY (uppler_product_id) REFERENCES uppler_product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE favorite_product DROP CONSTRAINT FK_8E1EAAC3AA17481D');
        $this->addSql('ALTER TABLE "saved_cart" DROP CONSTRAINT FK_59C7AA29B6B5FBA');
        $this->addSql('ALTER TABLE saved_cart_product DROP CONSTRAINT FK_A536D30C90D40228');
        $this->addSql('DROP TABLE favorite_product');
        $this->addSql('DROP TABLE "saved_cart"');
        $this->addSql('DROP TABLE saved_cart_product');
    }
}
