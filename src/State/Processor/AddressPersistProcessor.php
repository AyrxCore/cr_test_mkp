<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\Factory\AddressFactory;
use App\Service\UpplerAddressService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

readonly class AddressPersistProcessor implements ProcessorInterface
{
    public function __construct(private UpplerAddressService $upplerAddressService, private AddressFactory $addressFactory)
    {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        try {
            if (($context['operation'] ?? null) instanceof Put) {
                $this->upplerAddressService->updateAddress($data);
            } elseif (($context['operation'] ?? null) instanceof Post) {
                return $this->addressFactory->create($this->upplerAddressService->createAddress($data));
            } else {
                throw new BadRequestHttpException();
            }

            return $data;
        } catch (\Throwable $e) {
            throw new BadRequestHttpException();
        }
    }
}
