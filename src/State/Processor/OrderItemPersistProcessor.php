<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProcessorInterface;
use App\Service\UpplerCartService;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

readonly class OrderItemPersistProcessor implements ProcessorInterface
{
    public function __construct(private UpplerCartService $upplerCartService)
    {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof Patch) {
            $this->upplerCartService->updateOrderItemQuantity(
                $data->getId(),
                $data->getQuantity(),
            );

            return $data;
        }
        throw new BadRequestException('Persist error');
    }
}
