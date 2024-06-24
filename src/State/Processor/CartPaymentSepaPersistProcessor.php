<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProcessorInterface;
use App\Service\UpplerCartService;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CartPaymentSepaPersistProcessor implements ProcessorInterface
{
    public function __construct(private UpplerCartService $upplerCartService)
    {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        if (($context['operation'] ?? null) instanceof Patch) {
            if ($data->getMandateId() === null && \in_array(null, [$data->getIban(), $data->getBic(), $data->getOwnerName(), $data->getPhone()], true)) {
                throw new BadRequestException('Sepa informations missing');
            }

            $result = $this->upplerCartService->setSepaInformations($data);
            if ($result === false || ($data->getMandateId() === null && !isset($result['signing_url']) && !isset($result['errors']))) {
                \Sentry\captureMessage('URL de signature SEPA non présente');
                throw new BadRequestException('Update cart sepa payment error');
            }

            $response = new JsonResponse($result);
            if (isset($result['errors'])) {
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            }

            return $response;
        }
        throw new BadRequestException('Persist error');
    }
}
