<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\Invoice;
use App\Service\UpplerOrderService;
use Symfony\Contracts\Service\Attribute\Required;

class InvoiceDataProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{
    #[Required]
    public UpplerOrderService $upplerOrderService;

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Invoice::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): Invoice|null
    {
        ['headers' => $headers, 'content' => $content] = $this->upplerOrderService->getOrderInvoiceByIdAndUserId($id);
        if (isset($headers['content-disposition'])) {
            $contentDispositionHeader = $headers['content-disposition'];
            preg_match('/filename="(.*)"/', $contentDispositionHeader[0], $matches);
            if (isset($matches[1])) {
                $filename = $matches[1];

                $invoice = new Invoice();
                $invoice->setId($id);
                $invoice->setName($filename);
                $invoice->setContent($content);

                return $invoice;
            }
        }

        return null;
    }
}
