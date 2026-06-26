<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Factory\DjustCartFactory;
use App\Service\Djust\DjustCartService;

class CartProvider implements ProviderInterface
{
    public function __construct(private readonly DjustCartService $djustCartService, private readonly DjustCartFactory $djustCartFactory)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $commercialOrder = $this->djustCartService->getCart();

        if ($commercialOrder !== null && isset($commercialOrder['reference'])) {
            $blockingIds = $this->djustCartService->syncAndCleanCart($commercialOrder['reference']);
            if (!empty($blockingIds)) {
                $commercialOrder = $this->djustCartService->getCart();
            }
        }

        return $this->djustCartFactory->createFromCommercialOrder(
            $commercialOrder,
        );
    }
}
