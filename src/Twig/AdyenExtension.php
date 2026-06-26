<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdyenExtension extends AbstractExtension
{
    public function __construct(
        #[Autowire(env: 'ADYEN_CLIENT_KEY')]
        private readonly string $clientKey,
        #[Autowire(env: 'ADYEN_ENVIRONMENT')]
        private readonly string $environment,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('adyen_client_key', [$this, 'getClientKey']),
            new TwigFunction('adyen_environment', [$this, 'getEnvironment']),
        ];
    }

    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }
}

