<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MapExtension extends AbstractExtension
{
    public function __construct(
        #[Autowire(env: 'CARTO_API_KEY')]
        private readonly string $cartoApiKey,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('carto_api_key', [$this, 'getCartoApiKey']),
        ];
    }

    public function getCartoApiKey(): string
    {
        return $this->cartoApiKey;
    }
}
