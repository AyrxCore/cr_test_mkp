<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Partner;
use App\Entity\ShippingRule;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

class ShippingRuleFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['shippingRule'];
    }

    public function load(ObjectManager $manager): void
    {
        $shippingRulesData = [
            [
                'partnerId' => '9258568a-8ff7-11ec-bdfe-028bf10cd626',
                'type' => 'STANDARD',
                'rule' => [
                    'levels' => [
                        [
                            'franco_min_ht' => 0,
                            'franco_max_ht' => 50.45,
                            'fdp_ht' => 7,
                        ],
                    ],
                ],
            ],
            [
                'partnerId' => '35a3c1c0-5cf0-11ec-bee9-028bf10cd626',
                'type' => 'STEPS',
                'rule' => [
                    'levels' => [
                        [
                            'franco_min_ht' => 0,
                            'franco_max_ht' => 50.45,
                            'fdp_ht' => 7,
                        ],
                        [
                            'franco_min_ht' => 50.45,
                            'franco_max_ht' => 100.2,
                            'fdp_ht' => 3,
                        ]
                    ],
                ],
            ],
            [
                'partnerId' => '5674ce9a-ecbe-11ec-8253-025bcd73cb12',
                'type' => 'FIXED',
                'rule' => [
                    'levels' => [
                        [
                            'franco_min_ht' => 0,
                            'franco_max_ht' => 20,
                            'fdp_ht' => 5,
                        ],
                        [
                            'franco_min_ht' => 20,
                            'franco_max_ht' => null,
                            'fdp_ht' => 3,
                        ]
                    ],
                ],
            ],
            [
                'partnerId' => 'e1c2378e-5827-11ec-8262-021d5312eaee',
                'type' => 'FREE',
                'rule' => [],
            ],
            [
                'partnerId' => 'e01fc1e4-5827-11ec-a8ad-021d5312eaee',
                'type' => 'WEIGHT',
                'rule' => [
                    'weights' => [
                        [
                            'weight_min' => 0,
                            'weight_max' => 1,
                            'fdp_ht' => 3,
                            'franco_min_ht' => 0,
                            'franco_max_ht' => 10,
                        ],
                        [
                            'weight_min' => 1,
                            'weight_max' => 2,
                            'fdp_ht' => 6,
                            'franco_min_ht' => 0,
                            'franco_max_ht' => 10,
                        ],
                        [
                            'weight_min' => 2,
                            'weight_max' => 3,
                            'fdp_ht' => 9,
                            'franco_min_ht' => 0,
                            'franco_max_ht' => 10,
                        ],
                        [
                            'weight_min' => 3,
                            'weight_max' => 4,
                            'fdp_ht' => 12,
                            'franco_min_ht' => 0,
                            'franco_max_ht' => 10,
                        ],
                    ],
                ],
            ],
            [
                'partnerId' => 'a1b2c3d4-0000-0000-0000-000000000001',
                'type' => 'CATEGORY',
                'rule' => [
                    'levels' => [
                        [
                            'category' => 'test',
                            'fdp_ht' => 7,
                            'franco_max_ht' => 100,
                            'franco_min_ht' => 0,
                        ],
                        [
                            'category' => 'test2',
                            'fdp_ht' => 8,
                            'franco_max_ht' => 100,
                            'franco_min_ht' => 0,
                        ],
                    ],
                ],
            ],
            [
                'partnerId' => 'd175c094-5827-11ec-b22b-028bf10cd626',
                'type' => 'WEIGHT',
                'rule' => [
                    'weights' => [
                        [
                            'weight_min' => 0,
                            'weight_max' => 10,
                            'fdp_ht' => 3,
                            'franco_min_ht' => null,
                            'franco_max_ht' => null,
                        ],
                        [
                            'weight_min' => 10,
                            'weight_max' => 20,
                            'fdp_ht' => 6,
                            'franco_min_ht' => null,
                            'franco_max_ht' => null,
                        ],
                        [
                            'weight_min' => 20,
                            'weight_max' => 30,
                            'fdp_ht' => 9,
                            'franco_min_ht' => null,
                            'franco_max_ht' => null,
                        ],
                    ],
                ],
            ],
        ];

        $partnerRepository = $manager->getRepository(Partner::class);

        foreach ($shippingRulesData as $data) {
            $partner = $partnerRepository->find(Uuid::fromString($data['partnerId']));

            if (null === $partner) {
                continue;
            }

            $shippingRule = new ShippingRule();
            $shippingRule->setPartner($partner);
            $shippingRule->setType($data['type']);
            $shippingRule->setRule($data['rule']);

            $manager->persist($shippingRule);
        }

        $manager->flush();
    }
}
