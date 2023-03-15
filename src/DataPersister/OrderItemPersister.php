<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Dto\OrderItem;
use App\Service\UpplerCartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Contracts\Service\Attribute\Required;

class OrderItemPersister implements ContextAwareDataPersisterInterface
{
    #[Required]
    public UpplerCartService $upplerCartService;

    public function supports($data, array $context = []): bool
    {
        return $data instanceof OrderItem;
    }

    /**
     * @param OrderItem $data
     */
    public function persist($data, array $context = [])
    {
        if (($context['collection_operation_name'] ?? null) === 'POST') {
            $result = $this->upplerCartService->addItemToCart(
                $data->getCartId(),
                $data->getVariantId(),
                $data->getQuantity(),
            );

            if ($result) {
                $orderItem = new OrderItem();
                $orderItem->setId(11);
                return $orderItem;
            }

            throw new BadRequestException('Add item error');
        } else if (($context['item_operation_name'] ?? null) === 'update') {
            $result = $this->upplerCartService->updateOrderItemQuantity(
                $data->getId(),
                $data->getQuantity(),
            );
            if ($result) {
                return true;
            }

            throw new BadRequestException('Update quantity error');
        }
        throw new BadRequestException('Persist error');
    }

    public function remove($data, array $context = [])
    {
        $result = $this->upplerCartService->deleteOrderItem(
            $data->getId(),
        );

        if ($result) {
            return true;
        }

        throw new BadRequestException('Delete order item');
    }
}
