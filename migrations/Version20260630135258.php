<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260630135258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'MKP-1449: Change cart_savings.cart_id from INT to VARCHAR(255) for Djust cart references';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cart_savings ALTER COLUMN cart_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE cart_savings ALTER COLUMN cart_id DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cart_savings ALTER COLUMN cart_id TYPE INT USING cart_id::integer');
        $this->addSql('ALTER TABLE cart_savings ALTER COLUMN cart_id SET NOT NULL');
    }
}
