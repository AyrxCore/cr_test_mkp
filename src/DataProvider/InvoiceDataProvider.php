<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\Invoice;
use App\Factory\InvoiceFactory;
use App\Service\UpplerOrderService;
use Symfony\Contracts\Service\Attribute\Required;

class InvoiceDataProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{
    #[Required]
    public UpplerOrderService $upplerOrderService;

    public function __construct(private InvoiceFactory $invoiceFactory)
    {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Invoice::class;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): Invoice|null
    {
        $data = $this->upplerOrderService->getOrderInvoiceByIdAndUserId($id);
        $data['id'] = $id;

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
