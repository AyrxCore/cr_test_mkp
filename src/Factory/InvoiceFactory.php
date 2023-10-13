<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\Invoice;

class InvoiceFactory extends AbstractFactory
{
    public function create(array $data): Invoice
    {
        $invoice = new Invoice();
        $invoice->setId($data['id']);
        $invoice->setName($data['filename']);
        $invoice->setContent($data['content']);

        return $invoice;
    }
}
