<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260518000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create shipping_rule table and insert initial data (STANDARD, FREE, CATEGORY, STEPS, FIXED, WEIGHT)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE IF NOT EXISTS shipping_rule (
                id         UUID         NOT NULL,
                partner_id UUID         NOT NULL,
                type       VARCHAR(255) NOT NULL,
                rule       JSONB        NOT NULL,
                PRIMARY KEY (id),
                CONSTRAINT fk_shipping_rule_partner
                    FOREIGN KEY (partner_id) REFERENCES partner (id)
            )
        SQL);

        $this->addSql(<<<'SQL'
            INSERT INTO shipping_rule (id, type, rule, partner_id)
            SELECT v.id::uuid, v.type, v.rule::jsonb, v.partner_id::uuid
            FROM (VALUES
                -- STANDARD
                ('72c466b6-770a-485a-b930-df744e3389e5', 'STANDARD', '{"levels": [{"fdp_ht": 3.33, "franco_max_ht": 30, "franco_min_ht": 0}]}',             '2ab36ea6-270e-11ef-8e5c-0636cec1a392'),
                ('8d0d13f4-5fd9-45d0-ad88-d882d2cdce78', 'STANDARD', '{"levels": [{"fdp_ht": 18, "franco_max_ht": 300, "franco_min_ht": 0}]}',              '332436cc-a16f-11ed-ba5e-069c14af451a'),
                ('b7026907-c28f-42db-bd06-7f80ecaebb80', 'STANDARD', '{"levels": [{"fdp_ht": 15, "franco_max_ht": 50, "franco_min_ht": 0}]}',               '47ab1d14-5827-11ec-a13c-028bf10cd626'),
                ('178bdd4f-5805-4de8-b3a4-0e44322dbb4a', 'STANDARD', '{"levels": [{"fdp_ht": 19, "franco_max_ht": 1000, "franco_min_ht": 0}]}',             '4bb5bf2c-5827-11ec-a67b-028bf10cd626'),
                ('667d77f8-23cd-4be3-8bce-717dd1bf5d36', 'STANDARD', '{"levels": [{"fdp_ht": 8.9, "franco_max_ht": 150, "franco_min_ht": 0}]}',             '502e4798-1a83-11ee-8857-0af08f946010'),
                ('1f479e52-a033-4f2a-82d4-cab991c32248', 'STANDARD', '{"levels": [{"fdp_ht": 12.90, "franco_max_ht": 49, "franco_min_ht": 0}]}',            '52b6752a-0607-11ee-b678-0aa8df995aa4'),
                ('6729a0cc-3225-11ef-a1d4-0aa8df995aa4', 'STANDARD', '{"levels": [{"fdp_ht": 7, "franco_max_ht": 50, "franco_min_ht": 0}]}',                '9258568a-8ff7-11ec-bdfe-028bf10cd626'),
                ('15d69136-b26a-46ec-9e54-66775036b03c', 'STANDARD', '{"levels": [{"fdp_ht": 20, "franco_max_ht": 150, "franco_min_ht": 0}]}',              'ceb67538-5827-11ec-9fc1-028bf10cd626'),
                ('bc250bd0-cebc-457e-9ae5-5926f0f74d42', 'STANDARD', '{"levels": [{"fdp_ht": 9, "franco_max_ht": 90, "franco_min_ht": 0}]}',                'd199c1f6-5827-11ec-9838-028bf10cd626'),
                ('b86a1c01-558e-47ff-91fb-fbe7d6dacac1', 'STANDARD', '{"levels": [{"fdp_ht": 14.9, "franco_max_ht": 199, "franco_min_ht": 0}]}',            'dc27ee8a-765c-11ef-8049-0636cec1a392'),
                ('b27fab74-28af-4edc-8355-3e537124b384', 'STANDARD', '{"levels": [{"fdp_ht": 45, "franco_max_ht": 449, "franco_min_ht": 0}]}',              'e1d4512c-5826-11ec-a49c-028bf10cd626'),
                ('e8df8000-b031-4880-bd74-c55bd04d4355', 'STANDARD', '{"levels": [{"fdp_ht": 10, "franco_max_ht": 400, "franco_min_ht": 0}]}',              '4bcb1dfe-5bcf-11f0-879a-1f090211b6b2'),
                ('d6de0cb6-c4d7-4ef0-993b-ae1bedb98e91', 'STANDARD', '{"levels": [{"fdp_ht": 5.9, "franco_max_ht": 50, "franco_min_ht": 0}]}',             'e6ada84c-8289-11f0-aa2c-ad6db148622c'),
                -- FREE
                ('6aa3280f-6575-4e51-9a8f-e6efcda68d15', 'FREE',     '[]',                                                                                  '4a3817d4-405a-11f0-8fd5-87f4e4bc9ecb'),
                ('1f11252b-358d-612a-a306-91efe7e3accd', 'FREE',     '[]',                                                                                  'e1c2378e-5827-11ec-8262-021d5312eaee'),
                ('d7f5031f-2722-4af3-8e9e-f3da77ccafbd', 'FREE',     '[]',                                                                                  'e25d28f2-5827-11ec-9582-021d5312eaee'),
                -- CATEGORY
                ('1f1164f2-f57b-63f0-8b77-136c7677516d', 'CATEGORY', '{"levels": [{"fdp_ht": 18, "category": "COMEBACK18", "franco_max_ht": 1700, "franco_min_ht": 0}, {"fdp_ht": 20, "category": "COMEBACK20", "franco_max_ht": 1700, "franco_min_ht": 0}, {"fdp_ht": 30, "category": "COMEBACK30", "franco_max_ht": 1700, "franco_min_ht": 0}, {"fdp_ht": 40, "category": "COMEBACK40", "franco_max_ht": 1700, "franco_min_ht": 0}]}', '6729a0cc-3225-11ef-a1d4-0aa8df995aa4'),
                -- STEPS
                ('1f11252b-358a-6ed4-b0d9-91efe7e4accd', 'STEPS',    '{"levels": [{"fdp_ht": 18, "franco_max_ht": 50, "franco_min_ht": 0}, {"fdp_ht": 10, "franco_max_ht": 105, "franco_min_ht": 50}]}', 'd5c864bc-5827-11ec-999e-0af08f946010'),
                -- FIXED
                ('8a06797a-3c35-4dca-ae5b-a0301c32dacb', 'FIXED',    '{"levels": [{"fdp_ht": 34.99, "franco_max_ht": 300, "franco_min_ht": 0}, {"fdp_ht": 15, "franco_max_ht": null, "franco_min_ht": 300}]}', 'e09ed754-5827-11ec-b020-021d5312eaee'),
                -- WEIGHT
                ('287d436f-b599-48ac-b4c5-348f127d9d7f', 'WEIGHT',   '{"weights": [{"fdp_ht": 20, "weight_max": 1, "weight_min": 0}, {"fdp_ht": 24, "weight_max": 2, "weight_min": 1}, {"fdp_ht": 28, "weight_max": 3, "weight_min": 2}, {"fdp_ht": 32, "weight_max": 4, "weight_min": 3}, {"fdp_ht": 36, "weight_max": 5, "weight_min": 4}, {"fdp_ht": 40, "weight_max": 6, "weight_min": 5}, {"fdp_ht": 44, "weight_max": 7, "weight_min": 6}, {"fdp_ht": 48, "weight_max": 8, "weight_min": 7}, {"fdp_ht": 52, "weight_max": 9, "weight_min": 8}, {"fdp_ht": 56, "weight_max": 10, "weight_min": 9}, {"fdp_ht": 60, "weight_max": 11, "weight_min": 10}, {"fdp_ht": 64, "weight_max": 12, "weight_min": 11}, {"fdp_ht": 68, "weight_max": 13, "weight_min": 12}, {"fdp_ht": 72, "weight_max": 14, "weight_min": 13}, {"fdp_ht": 76, "weight_max": 15, "weight_min": 14}, {"fdp_ht": 80, "weight_max": 16, "weight_min": 15}, {"fdp_ht": 84, "weight_max": 17, "weight_min": 16}, {"fdp_ht": 88, "weight_max": 18, "weight_min": 17}, {"fdp_ht": 92, "weight_max": 19, "weight_min": 18}, {"fdp_ht": 96, "weight_max": 20, "weight_min": 19}, {"fdp_ht": 100, "weight_max": 21, "weight_min": 20}, {"fdp_ht": 104, "weight_max": 22, "weight_min": 21}, {"fdp_ht": 108, "weight_max": 23, "weight_min": 22}, {"fdp_ht": 112, "weight_max": 24, "weight_min": 23}, {"fdp_ht": 116, "weight_max": 25, "weight_min": 24}, {"fdp_ht": 120, "weight_max": 26, "weight_min": 25}, {"fdp_ht": 124, "weight_max": 27, "weight_min": 26}, {"fdp_ht": 128, "weight_max": 28, "weight_min": 27}, {"fdp_ht": 132, "weight_max": 29, "weight_min": 28}, {"fdp_ht": 136, "weight_max": 30, "weight_min": 29}, {"fdp_ht": 140, "weight_max": 31, "weight_min": 30}, {"fdp_ht": 144, "weight_max": 32, "weight_min": 31}, {"fdp_ht": 148, "weight_max": 33, "weight_min": 32}, {"fdp_ht": 152, "weight_max": 34, "weight_min": 33}, {"fdp_ht": 156, "weight_max": 35, "weight_min": 34}, {"fdp_ht": 160, "weight_max": 36, "weight_min": 35}, {"fdp_ht": 164, "weight_max": 37, "weight_min": 36}, {"fdp_ht": 168, "weight_max": 38, "weight_min": 37}, {"fdp_ht": 172, "weight_max": 39, "weight_min": 38}, {"fdp_ht": 176, "weight_max": 40, "weight_min": 39}, {"fdp_ht": 180, "weight_max": 41, "weight_min": 40}, {"fdp_ht": 184, "weight_max": 42, "weight_min": 41}, {"fdp_ht": 188, "weight_max": 43, "weight_min": 42}, {"fdp_ht": 192, "weight_max": 44, "weight_min": 43}, {"fdp_ht": 196, "weight_max": 45, "weight_min": 44}, {"fdp_ht": 200, "weight_max": 46, "weight_min": 45}, {"fdp_ht": 204, "weight_max": 47, "weight_min": 46}, {"fdp_ht": 208, "weight_max": 48, "weight_min": 47}, {"fdp_ht": 212, "weight_max": 49, "weight_min": 48}, {"fdp_ht": 216, "weight_max": 50, "weight_min": 49}]}', 'e01fc1e4-5827-11ec-a8ad-021d5312eaee')
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
        $this->addSql('DROP TABLE IF EXISTS shipping_rule');
    }
}

