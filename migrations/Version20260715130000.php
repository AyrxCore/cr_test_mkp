<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260715130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add shipping rules for Radior Bike (QUANTITY_STEPS) and Manergo (PERCENTAGE)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            DELETE FROM shipping_rule
            WHERE partner_id = '4a3817d4-405a-11f0-8fd5-87f4e4bc9ecb'
              AND EXISTS (SELECT 1 FROM partner WHERE id = '4a3817d4-405a-11f0-8fd5-87f4e4bc9ecb'::uuid)
        SQL);

        $this->addSql(<<<'SQL'
            INSERT INTO shipping_rule (id, type, rule, partner_id)
            SELECT v.id::uuid, v.type, v.rule::jsonb, v.partner_id::uuid
            FROM (VALUES
                -- QUANTITY_STEPS: Radior Bike
                ('6aa3280f-6575-4e51-9a8f-e6efcda68d15', 'QUANTITY_STEPS',
                 '{"levels": [{"quantity_min": 1, "quantity_max": 1, "fdp_ht": 83.33}, {"quantity_min": 2, "quantity_max": 2, "fdp_ht": 166.66}, {"quantity_min": 3, "quantity_max": 3, "fdp_ht": 249.99}, {"quantity_min": 4, "quantity_max": 999999, "fdp_ht": 0}]}',
                 '4a3817d4-405a-11f0-8fd5-87f4e4bc9ecb'),
                -- PERCENTAGE: Manergo
                ('1f11252b-358d-612a-a306-91efe7e3a000', 'PERCENTAGE',
                 '{"percentage": 10}',
                 '6b5b00f8-f44b-11ef-94fb-069c14af451a')
            ) AS v(id, type, rule, partner_id)
            WHERE EXISTS (SELECT 1 FROM partner WHERE id = v.partner_id::uuid)
            ON CONFLICT (id) DO UPDATE SET
                type       = EXCLUDED.type,
                rule       = EXCLUDED.rule,
                partner_id = EXCLUDED.partner_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            DELETE FROM shipping_rule
            WHERE partner_id IN (
                '4a3817d4-405a-11f0-8fd5-87f4e4bc9ecb',
                '6b5b00f8-f44b-11ef-94fb-069c14af451a'
            )
        SQL);

        $this->addSql(<<<'SQL'
            INSERT INTO shipping_rule (id, type, rule, partner_id)
            SELECT '6aa3280f-6575-4e51-9a8f-e6efcda68d15'::uuid, 'FREE', '[]'::jsonb, '4a3817d4-405a-11f0-8fd5-87f4e4bc9ecb'::uuid
            WHERE EXISTS (SELECT 1 FROM partner WHERE id = '4a3817d4-405a-11f0-8fd5-87f4e4bc9ecb'::uuid)
            ON CONFLICT (id) DO UPDATE SET
                type       = EXCLUDED.type,
                rule       = EXCLUDED.rule,
                partner_id = EXCLUDED.partner_id
        SQL);
    }
}
