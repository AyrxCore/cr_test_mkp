<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Dto\OrderItem;
use App\Service\UpplerCartService;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class OrderItemPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(private UpplerCartService $upplerCartService)
    {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof OrderItem;
    }

    /**
     * @param OrderItem $data
     */
    public function persist($data, array $context = [])
    {
        if (($context['item_operation_name'] ?? null) === 'update') {
            return $this->upplerCartService->updateOrderItemQuantity(
                $data->getId(),
                $data->getQuantity(),
            );
        }
        throw new BadRequestException('Persist error');
    }

    public function remove($data, array $context = [])
    {
        return $this->upplerCartService->deleteOrderItem(
            $data->getId(),
        );
    }
}
