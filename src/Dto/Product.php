<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation;
use App\State\Provider\ProductProvider;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/products/{id}',
            requirements: ['id' => '\\d+'],
            normalizationContext: ['groups' => ['product:get']]
        ),
        new GetCollection(
            openapi: new Operation(
                summary: 'Get remote products list',
                description: 'It gets a list of products from remote data provider'
            ),
            normalizationContext: ['groups' => ['products:get']]
        ),
    ],
    provider: ProductProvider::class
)]
final class Product
{
    public const string PROCESS_STATUS_NOT_ACTIVATED = 'NOT_ACTIVATED';
    public const string PROCESS_STATUS_PENDING = 'PENDING';
    public const string PROCESS_STATUS_ACTIVATED = 'ACTIVATED';
    public const array PROCESS_STATUS
        = [
            self::PROCESS_STATUS_NOT_ACTIVATED,
            self::PROCESS_STATUS_PENDING,
            self::PROCESS_STATUS_ACTIVATED,
        ];

    public const string HOME_TOP_VENTE = 'home-top-vente';
    public const string HOME_SELECTION = 'home-selection';

    #[ApiProperty(identifier: true)]
    #[Groups(['products:get', 'product:get'])]
    private ?int $id = null;

    #[Groups(['products:get', 'product:get'])]
    private ?string $name;

    #[Groups(['product:get'])]
    private ?string $reference;

    #[Groups(['products:get', 'product:get'])]
    private ?string $slug;

    #[Groups(['products:get', 'product:get'])]
    private ?string $description;

    #[Groups(['product:get'])]
    private array $categories;

    #[Groups(['products:get', 'product:get'])]
    private array $images;

    #[Groups(['product:get'])]
    private array $options;

    #[Groups(['products:get', 'product:get'])]
    private array $properties;

    #[Groups(['products:get', 'product:get'])]
    private array $variants;

    #[Groups(['products:get', 'product:get'])]
    private ?int $defaultVariantId;

    #[Groups(['products:get', 'product:get'])]
    private ?array $defaultVariantOptions;

    #[Groups(['products:get', 'product:get'])]
    private ?float $priceReference = 0.0;

    #[Groups(['products:get', 'product:get'])]
    private ?float $percent = 0;

    #[Groups(['products:get', 'product:get'])]
    private ?float $price = null;

    #[Groups(['products:get', 'product:get'])]
    private bool $isAccordCadre = false;

    #[Groups(['products:get', 'product:get'])]
    private bool $sellable = true;

    #[Groups(['products:get', 'product:get'])]
    private bool $notSellableFormWithMessage = false;

    #[Groups(['products:get', 'product:get'])]
    private ?Seller $seller;

    #[Groups(['product:get'])]
    private ?AccountAccordCadre $accountAccordCadre;

    #[Groups(['products:get', 'product:get'])]
    private array $favorites = [];

    #[Groups(['product:get'])]
    private ?int $quantity = 0;

    #[Groups(['products:get', 'product:get'])]
    private bool $newTarifNotification = false;

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

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): void
    {
        $this->reference = $reference;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): void
    {
        $this->images = $images;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function getVariants(): array
    {
        return $this->variants;
    }

    public function setVariants(array $variants): void
    {
        $this->variants = $variants;
    }

    public function getDefaultVariantId(): int
    {
        return $this->defaultVariantId;
    }

    public function setDefaultVariantId(?int $defaultVariantId): void
    {
        $this->defaultVariantId = $defaultVariantId;
    }

    public function getDefaultVariantOptions(): ?array
    {
        return $this->defaultVariantOptions;
    }

    public function setDefaultVariantOptions(?array $defaultVariantOptions): void
    {
        $this->defaultVariantOptions = $defaultVariantOptions;
    }

    public function getPriceReference(): ?float
    {
        return $this->priceReference;
    }

    public function setPriceReference(?float $priceReference): void
    {
        $this->priceReference = $priceReference;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    public function setSeller(?Seller $seller): void
    {
        $this->seller = $seller;
    }

    public function getPercent(): ?float
    {
        return $this->percent;
    }

    public function setPercent(?float $percent): void
    {
        $this->percent = $percent;
    }

    public function getIsAccordCadre(): bool
    {
        return $this->isAccordCadre;
    }

    public function setIsAccordCadre(bool $isAccordCadre): void
    {
        $this->isAccordCadre = $isAccordCadre;
    }

    public function isSellable(): bool
    {
        return $this->sellable;
    }

    public function setSellable(bool $sellable): void
    {
        $this->sellable = $sellable;
    }

    public function isNotSellableFormWithMessage(): bool
    {
        return $this->notSellableFormWithMessage;
    }

    public function setNotSellableFormWithMessage(bool $notSellableFormWithMessage): void
    {
        $this->notSellableFormWithMessage = $notSellableFormWithMessage;
    }

    public function getAccountAccordCadre(): ?AccountAccordCadre
    {
        return $this->accountAccordCadre;
    }

    public function setAccountAccordCadre(?AccountAccordCadre $accountAccordCadre): void
    {
        $this->accountAccordCadre = $accountAccordCadre;
    }

    public function getFavorites(): array
    {
        return $this->favorites;
    }

    public function setFavorites(array $favorites): void
    {
        $this->favorites = $favorites;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function isNewTarifNotification(): bool
    {
        return $this->newTarifNotification;
    }

    public function setNewTarifNotification(bool $newTarifNotification): void
    {
        $this->newTarifNotification = $newTarifNotification;
    }
}
