<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProcessorInterface;
use App\Service\UpplerCartService;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

readonly class CartAddressPersistProcessor implements ProcessorInterface
{
    public function __construct(private UpplerCartService $upplerCartService)
    {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        try {
            if (($context['operation'] ?? null) instanceof Patch) {
                $this->upplerCartService->updateCartAddress(
                    $data->getId(),
                    $data->getShippingAddressId(),
                    $data->getBillingAddressId(),
                );

                return $data;
            }
        } catch (\Throwable $e) {
            throw new BadRequestException('Update cart addresses method error');
        }
    }
}
