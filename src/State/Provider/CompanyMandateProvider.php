<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Factory\CompanyMandateFactory;
use App\Service\UpplerBuyerCompanyService;

class CompanyMandateProvider implements ProviderInterface
{
    public function __construct(
        private CompanyMandateFactory $mandateFactory,
        private UpplerBuyerCompanyService $upplerCompanyService,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->mandateFactory->createAndAddToCollection($this->upplerCompanyService->getExistingMandates());
    }
}
