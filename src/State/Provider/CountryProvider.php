<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Factory\CountryFactory;
use App\Service\UpplerCountryService;

readonly class CountryProvider implements ProviderInterface
{
    public function __construct(private UpplerCountryService $upplerCountryService, private CountryFactory $countryFactory)
    {
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->countryFactory->createAndAddToCollection($this->upplerCountryService->getCountries());
    }
}
