<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260622113610 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'MKP-1449: Change cart_savings.order_id from INT to VARCHAR(255) for Djust cron analytics sync';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cart_savings ALTER order_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE cart_savings ALTER order_id DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cart_savings ALTER order_id TYPE INT USING order_id::integer');
        $this->addSql('ALTER TABLE cart_savings ALTER order_id SET NOT NULL');
    }
}
