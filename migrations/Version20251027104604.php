<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251027104604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE account ADD djust_customer_account_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE account ADD djust_customer_user_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE account ADD djust_username VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE account ADD djust_password VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE account ALTER uppler_user_id DROP NOT NULL');
        $this->addSql('ALTER TABLE account ALTER uppler_sub_account_id DROP NOT NULL');
        $this->addSql('ALTER TABLE account ALTER uppler_company_id DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE account DROP djust_customer_account_id');
        $this->addSql('ALTER TABLE account DROP djust_customer_user_id');
        $this->addSql('ALTER TABLE account DROP djust_username');
        $this->addSql('ALTER TABLE account DROP djust_password');
        $this->addSql('ALTER TABLE account ALTER uppler_user_id SET NOT NULL');
        $this->addSql('ALTER TABLE account ALTER uppler_sub_account_id SET NOT NULL');
        $this->addSql('ALTER TABLE account ALTER uppler_company_id SET NOT NULL');
    }
}
