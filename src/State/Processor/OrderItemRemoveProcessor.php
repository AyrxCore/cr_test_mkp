<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Service\UpplerCartService;

readonly class OrderItemRemoveProcessor implements ProcessorInterface
{
    public function __construct(private UpplerCartService $upplerCartService)
    {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $this->upplerCartService->deleteOrderItem(
            $data->getId(),
        );

        return $data;
    }
}
