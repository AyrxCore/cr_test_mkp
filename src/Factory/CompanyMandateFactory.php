<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\CompanyMandate;

class CompanyMandateFactory extends AbstractFactory
{
    public function create(array $data): CompanyMandate
    {
        $mandate = new CompanyMandate();
        $mandate->setId($data['id']);
        $mandate->setIban($data['iban']);
        $mandate->setCreatedAt($data['created_at']);

        return $mandate;
    }
}
