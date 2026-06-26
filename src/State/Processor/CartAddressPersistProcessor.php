<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\CartAddress;
use App\Service\Djust\DjustCartService;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

readonly class CartAddressPersistProcessor implements ProcessorInterface
{
    public function __construct(
        private DjustCartService $djustCartService
    ) {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): CartAddress
    {
        if (!$data instanceof CartAddress) {
            throw new BadRequestHttpException('Invalid data type');
        }

        if (!$operation instanceof Patch) {
            throw new BadRequestException('Only PATCH operation is supported');
        }

        try {
            $cartId = $data->getCartId();

            if (!$cartId) {
                throw new BadRequestHttpException('Cart ID is required');
            }

            if ($data->getBillingAddressExternalId() !== null) {
                $this->djustCartService->updateCartBillingAddress(
                    $cartId,
                    $data->getBillingAddressExternalId()
                );
            }

            if ($data->getShippingAddressExternalId() !== null) {
                $this->djustCartService->updateCartShippingAddress(
                    $cartId,
                    $data->getShippingAddressExternalId()
                );
            }

            return $data;
        } catch (\Throwable $e) {
            throw new BadRequestHttpException(
                sprintf('Failed to update cart addresses: %s', $e->getMessage())
            );
        }
    }
}
