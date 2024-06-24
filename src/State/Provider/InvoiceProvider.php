<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Factory\InvoiceFactory;
use App\Service\UpplerOrderService;

readonly class InvoiceProvider implements ProviderInterface
{
    public function __construct(private InvoiceFactory $invoiceFactory, private UpplerOrderService $upplerOrderService)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $data = $this->upplerOrderService->getOrderInvoiceByIdAndUserId($uriVariables['id']);
        $data['id'] = $uriVariables['id'];

        if (isset($data['headers']['content-disposition'])) {
            $contentDispositionHeader = $data['headers']['content-disposition'];
            \preg_match('/filename="(.*)"/', $contentDispositionHeader[0], $matches);
            if (isset($matches[1])) {
                $data['filename'] = $matches[1];

                return $this->invoiceFactory->create($data);
            }
        }

        return null;
    }
}
