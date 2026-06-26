<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\Factory\AddressFactory;
use App\Service\Djust\DjustAddressService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

readonly class AddressPersistProcessor implements ProcessorInterface
{
    public function __construct(private DjustAddressService $djustAddressService, private AddressFactory $addressFactory)
    {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        try {
            if ($operation instanceof Put) {
                $this->djustAddressService->updateAddress($data);
            } elseif ($operation instanceof Post) {
                return $this->addressFactory->create($this->djustAddressService->createAddress($data));
            } else {
                throw new BadRequestHttpException();
            }

            return $data;
        } catch (HttpException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new BadRequestHttpException();
        }
    }
}
