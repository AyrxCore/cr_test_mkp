<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Dto\OrderShipping;
use App\Service\UpplerCartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Contracts\Service\Attribute\Required;

class OrderShippingPersister implements ContextAwareDataPersisterInterface
{
    #[Required]
    public UpplerCartService $upplerCartService;

    public function supports($data, array $context = []): bool
    {
        return $data instanceof OrderShipping;
    }

    /**
     * @param OrderShipping $data
     */
    public function persist($data, array $context = [])
    {
        if (($context['item_operation_name'] ?? null) === 'update') {
            $result = $this->upplerCartService->setShippingMethod(
                $data->getCartId(),
                $data->getId(),
                $data->getShippingId(),
            );
            if ($result) {
                return true;
            }

            throw new BadRequestException('Update shipping method error');
        }
        throw new BadRequestException('Persist error');
    }

    public function remove($data, array $context = [])
    {
    }
}
