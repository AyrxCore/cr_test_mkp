<?php

declare(strict_types=1);

namespace App\DataFixtures\Factory;

use App\DataFixtures\Factory\Trait\BeforeInstantiateTrait;
use App\Entity\ChannelParameter;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<ChannelParameter>
 */
final class ChannelParameterFactory extends PersistentObjectFactory
{
    use BeforeInstantiateTrait;

    public static function class(): string
    {
        return ChannelParameter::class;
    }

    protected function defaults(): array
    {
        return [
            'logo' => self::faker()->imageUrl(width: 160, height: 60),
            'favicon' => self::faker()->imageUrl(width: 16, height: 16),
            'email' => self::faker()->email(),
            'phoneNumber' => self::faker()->e164PhoneNumber(),
            'legalTerms' => self::faker()->url(),
            'generalTermsOfUse' => self::faker()->url(),
            'privacyPolicy' => self::faker()->url(),
            'primaryColor' => self::faker()->hexColor(),
            'secondaryColor' => self::faker()->hexColor(),
            'textColor' => self::faker()->hexColor(),
            'whiteLabel' => true,
        ];
    }

    protected function initialize(): static
    {
        return $this->beforeInstantiate($this->convertIdAttributes());
    }
}
