<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\Api\SellerPromotions;
use App\State\Provider\SellerProvider;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/sellers/{id}',
            requirements: ['id' => '\\d+']
        ),
        new Get(
            uriTemplate: '/sellers/{id}/promotions',
            requirements: ['id' => '\\d+'],
            controller: SellerPromotions::class,
            openapiContext: ['summary' => 'Add multiple item to cart']
        ),
        new GetCollection(
            openapiContext: ['summary' => 'Get remote sellers list', 'description' => 'It gets a list of sellers from remote data provider']
        )
    ],
    provider: SellerProvider::class
)]
final class Seller
{
    #[ApiProperty(identifier: true)]
    #[Groups(['products:get', 'product:get'])]
    private ?int $id = null;
    #[Groups(['products:get', 'product:get'])]
    private ?string $name;
    #[Groups(['products:get', 'product:get'])]
    private ?string $corporateName;
    #[Groups(['products:get', 'product:get'])]
    private ?string $description;
    #[Groups(['products:get', 'product:get'])]
    private ?string $avatar;
    #[Groups(['products:get', 'product:get'])]
    private ?array $tos;

    #[Groups(['products:get'])]
    private ?int $productCount;

    #[Groups(['products:get'])]
    private ?bool $checked;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getCorporateName(): ?string
    {
        return $this->corporateName;
    }

    public function setCorporateName(?string $corporateName): void
    {
        $this->corporateName = $corporateName;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getTos(): ?array
    {
        return $this->tos;
    }

    public function setTos(?array $tos): void
    {
        $this->tos = $tos;
    }

    public function getProductCount(): ?int
    {
        return $this->productCount;
    }

    public function setProductCount(?int $productCount): void
    {
        $this->productCount = $productCount;
    }

    public function getChecked(): ?bool
    {
        return $this->checked;
    }

    public function setChecked(?bool $checked): void
    {
        $this->checked = $checked;
    }
}
