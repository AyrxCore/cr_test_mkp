<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\CartPayment;
use App\Service\UpplerCartService;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;

readonly class CartPaymentPersistProcessor implements ProcessorInterface
{
    public function __construct(private UpplerCartService $upplerCartService)
    {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        if (($context['operation'] ?? null) instanceof Patch) {
            $result = $this->upplerCartService->setPaymentMethod(
                $data->getId(),
                $data->getPaymentMethodId(),
            );
            if (!$result || ($data->getPaymentMethodId() === CartPayment::CART_PAYMENT_CB && $result['payment_url'] === null)) {
                \Sentry\captureMessage('URL de paiement non présente');
                throw new BadRequestException('Update cart payment error');
            }

            return new JsonResponse($result);
        }
        throw new BadRequestException('Persist error');
    }
}
