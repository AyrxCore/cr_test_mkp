<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory;

use App\Entity\AdherentTarifShowcase;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

final class AdherentTarifShowcaseFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return AdherentTarifShowcase::class;
    }

    protected function defaults(): array
    {
        return [
            'adherent' => AdherentFactory::new(),
            'accordId' => Uuid::v4(),
            'tarifId' => Uuid::v4(),
            'contactRequested' => false,
        ];
    }
}
