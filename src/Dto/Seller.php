<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation;
use App\Controller\Api\SellerPromotions;
use App\State\Provider\SellerProvider;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/sellers/{id}',
            requirements: ['id' => '\\d+'],
        ),
        new Get(
            uriTemplate: '/sellers/{id}/promotions',
            requirements: ['id' => '\\d+'],
            controller: SellerPromotions::class,
            openapi: new Operation(
                summary: 'Get seller promotions"'
            )
        ),
        new GetCollection(
            openapi: new Operation(
                summary: 'Get remote sellers list',
                description: 'It gets a list of sellers from remote data provider'
            )
        ),
    ],
    provider: SellerProvider::class
)]
class Seller
{
    #[ApiProperty(identifier: true)]
    #[Groups(['products:get', 'product:get'])]
    private ?string $id = null;

    #[Groups(['products:get', 'product:get'])]
    private ?string $externalId = null;

    #[Groups(['products:get', 'product:get'])]
    private ?string $name = null;

    #[Groups(['products:get', 'product:get'])]
    private ?string $corporateName = null;

    #[Groups(['products:get', 'product:get'])]
    private ?string $description = null;

    #[Groups(['products:get', 'product:get'])]
    private ?string $avatar = null;

    #[Groups(['products:get', 'product:get'])]
    private ?string $tos = null;

    #[Groups(['products:get', 'product:get'])]
    private ?string $supplierDeliveryInfo = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCorporateName(): ?string
    {
        return $this->corporateName;
    }

    public function setCorporateName(?string $corporateName): self
    {
        $this->corporateName = $corporateName;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getTos(): ?string
    {
        return $this->tos;
    }

    public function setTos(?string $tos): self
    {
        $this->tos = $tos;

        return $this;
    }

    public function getSupplierDeliveryInfo(): ?string
    {
        return $this->supplierDeliveryInfo;
    }

    public function setSupplierDeliveryInfo(?string $supplierDeliveryInfo): void
    {
        $this->supplierDeliveryInfo = $supplierDeliveryInfo;
    }
}
