<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Order;
use App\Factory\OrderFactory;
use App\Service\Djust\DjustOrderService;

readonly class OrderProvider implements ProviderInterface
{
    public function __construct(
        private DjustOrderService $djustOrderService,
        private OrderFactory $orderFactory
    ) {
    }

    private const HIDDEN_STATUSES = ['CREATING', 'DRAFT_ORDER', 'DRAFT_ORDER_ON_HOLD'];

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            $remoteOrders = $this->djustOrderService->getOrders(['sort' => 'createdAt:desc']);
            $visibleOrders = \array_values(\array_filter($remoteOrders, $this->isVisible(...)));

            return $this->orderFactory->createAndAddToCollection($visibleOrders);
        }

        $remoteOrder = $this->djustOrderService->getOrderById((string) $uriVariables['id']);

        if ($remoteOrder === null || !$this->isVisible($remoteOrder)) {
            return null;
        }

        return $this->orderFactory->create($remoteOrder);
    }

    private function isVisible(array $remoteOrder): bool
    {
        $status = $remoteOrder['orderLogistics'][0]['status'] ?? 'DRAFT_ORDER';

        return !\in_array($status, self::HIDDEN_STATUSES, true);
    }
}
