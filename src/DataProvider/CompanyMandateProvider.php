<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\CompanyMandate;
use App\Factory\CompanyMandateFactory;
use App\Service\UpplerBuyerCompanyService;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CompanyMandateProvider implements RestrictedDataProviderInterface, CollectionDataProviderInterface
{
    public function __construct(
        private CompanyMandateFactory $mandateFactory,
        private NormalizerInterface $normalizer,
        private UpplerBuyerCompanyService $upplerCompanyService,
    ) {
    }

    public function getCollection(string $resourceClass, string $operationName = null): array
    {
        return $this->mandateFactory->createAndAddToCollection($this->upplerCompanyService->getExistingMandates());
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === CompanyMandate::class;
    }
}
