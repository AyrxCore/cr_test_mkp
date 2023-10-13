<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\Seller;

class SellerFactory extends AbstractFactory
{
    public function create(array $data): Seller
    {
        $seller = new Seller();
        $seller->setId($data['id']);
        $seller->setName($data['name']);

        if (isset($data['description']['default'])) {
            $seller->setDescription($data['description']['default']);
        }

        if (isset($data['avatar_url'])) {
            $seller->setAvatar($data['avatar_url']);
        }

        if (isset($data['count'])) {
            $seller->setProductCount($data['count']);
        }

        if (isset($data['checked'])) {
            $seller->setChecked($data['checked']);
        }

        if (isset($data['tos'])) {
            $seller->setTos(\json_decode(\json_encode($data['tos']), true));
        }

        return $seller;
    }
}
