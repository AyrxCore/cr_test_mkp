<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Dto\CartPayment;
use App\Service\UpplerCartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Service\Attribute\Required;

class CartPaymentPersister implements ContextAwareDataPersisterInterface
{
    #[Required]
    public UpplerCartService $upplerCartService;

    public function supports($data, array $context = []): bool
    {
        return $data instanceof CartPayment;
    }

    /**
     * @param CartPayment $data
     */
    public function persist($data, array $context = [])
    {
        if (($context['item_operation_name'] ?? null) === 'update') {
            $result = $this->upplerCartService->setPaymentMethod(
                $data->getId(),
                $data->getPaymentMethodId(),
            );
            if ($result) {
                return new JsonResponse($result);
            }

            throw new BadRequestException('Update cart payment error');
        }
        throw new BadRequestException('Persist error');
    }

    public function remove($data, array $context = [])
    {
    }
}
