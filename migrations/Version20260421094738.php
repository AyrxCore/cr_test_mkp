<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260421094738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'MKP-1359 — Add maintenance_whitelist_ips setting (empty by default, update via SQL to bypass maintenance mode)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO public.setting (id, name, value) VALUES (gen_random_uuid(), 'maintenance_whitelist_ips', '')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM public.setting WHERE name = 'maintenance_whitelist_ips'");
    }
}
