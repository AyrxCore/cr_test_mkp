<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Dto\CartPayment;
use App\Service\UpplerCartService;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Service\Attribute\Required;

class CartPaymentPersister implements ContextAwareDataPersisterInterface
{
    #[Required]
    public UpplerCartService $upplerCartService;

    #[Required]
    public Security $security;

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
                if (is_null($result->payment_url)) {
                    $user = $this->security->getUser();
                    \Sentry\withScope(function (\Sentry\State\Scope $scope) use ($user): void {
                        $scope->setUser(['email' => $user->getEmail()]);
                        \Sentry\captureMessage('URL de paiement non présente');
                    });
                }
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
