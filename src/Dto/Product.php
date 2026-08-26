<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Dto\AccordCadre\AccordCadreContent;
use App\State\Provider\ProductProvider;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/products/{id}',
            normalizationContext: ['groups' => ['product:get']]
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['products:get']]
        ),
    ],
    provider: ProductProvider::class
)]
class Product
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
    private ?string $id = null;

    #[Groups(['products:get', 'product:get'])]
    private ?string $name;

    #[Groups(['product:get'])]
    private ?string $reference;

    #[Groups(['products:get', 'product:get'])]
    private ?string $slug;

    #[Groups(['products:get', 'product:get'])]
    private ?string $description;

    #[Groups(['product:get'])]
    private array $categories = [];

    #[Groups(['products:get', 'product:get'])]
    private array $images = [];

    #[Groups(['product:get'])]
    private array $options = [];

    #[Groups(['products:get', 'product:get'])]
    private array $properties = [];

    #[Groups(['products:get', 'product:get'])]
    private array $tags = [];

    /**
     * @var array<int, Variant>
     */
    #[Groups(['products:get', 'product:get'])]
    private array $variants = [];

    #[Groups(['products:get', 'product:get'])]
    private string $defaultVariantId;

    #[Groups(['products:get', 'product:get'])]
    private ?float $priceReference = 0.0;

    #[Groups(['products:get', 'product:get'])]
    private ?float $percent = 0;

    #[Groups(['products:get', 'product:get'])]
    private ?float $price = null;

    #[Groups(['products:get', 'product:get'])]
    private bool $notSellableFormWithMessage = false;

    #[Groups(['products:get', 'product:get'])]
    private ?string $productType = null;

    #[Groups(['products:get', 'product:get'])]
    private ?string $productTopLabel = null;

    #[Groups(['products:get', 'product:get'])]
    private ?string $productPricingPhrase = null;

    #[Groups(['products:get', 'product:get'])]
    private ?Seller $seller;

    #[Groups(['products:get', 'product:get'])]
    private ?AccountAccordCadre $accountAccordCadre = null;

    #[Groups(['products:get', 'product:get'])]
    private array $favorites = [];

    #[Groups(['product:get'])]
    private ?int $quantity = 0;

    #[Groups(['products:get', 'product:get'])]
    private bool $newTarifNotification = false;

    /**
     * Quantité minimum de commande.
     */
    #[Groups(['products:get', 'product:get'])]
    private int $minOrderQuantity = 1;

    /**
     * Quantité maximum de commande.
     */
    #[Groups(['products:get', 'product:get'])]
    private int $maxOrderQuantity = 999;

    #[Groups(['product:get'])]
    private array $attachments = [];

    #[Groups(['products:get', 'product:get'])]
    private ?AccordCadreContent $accordCadreContent = null;

    #[Groups(['products:get', 'product:get'])]
    private ?string $tarifId = null;

    #[Groups(['products:get', 'product:get'])]
    private ?string $accordId = null;

    /**
     * Franco de commande (seuil minimum pour livraison gratuite du produit).
     */
    #[Groups(['products:get', 'product:get'])]
    private ?float $franco = null;

    /**
     * Frais de livraison si commande inférieure au franco (frais de livraison du produit).
     */
    #[Groups(['products:get', 'product:get'])]
    private ?float $shippingCost = null;

    #[Groups(['products:get', 'product:get'])]
    private ?string $sku = null;

    /**
     * @var array<int, array{quantity: int, price: float, priceReference: float}>
     */
    #[Groups(['products:get', 'product:get'])]
    private array $priceRanges = [];

    #[Groups(['products:get', 'product:get'])]
    private ?string $shippingCategory = null;

    #[Groups(['products:get', 'product:get'])]
    private ?float $weight = null;

    #[Groups(['products:get', 'product:get'])]
    private bool $formWithMessageFat = false;

    #[Groups(['products:get', 'product:get'])]
    private ?string $externalId = null;

    #[Groups(['products:get', 'product:get'])]
    private ?float $ecoTax = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): self
    {
        $this->id = $id;

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

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

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

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function setCategories(array $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): self
    {
        $this->images = $images;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setProperties(array $properties): self
    {
        $this->properties = $properties;

        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getVariants(): array
    {
        return $this->variants;
    }

    public function setVariants(array $variants): self
    {
        $this->variants = $variants;

        return $this;
    }

    public function getDefaultVariantId(): string
    {
        return $this->defaultVariantId;
    }

    public function setDefaultVariantId(string $defaultVariantId): self
    {
        $this->defaultVariantId = $defaultVariantId;

        return $this;
    }

    public function getPriceReference(): ?float
    {
        return $this->priceReference;
    }

    public function setPriceReference(?float $priceReference): self
    {
        $this->priceReference = $priceReference;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    public function setSeller(?Seller $seller): self
    {
        $this->seller = $seller;

        return $this;
    }

    public function getPercent(): ?float
    {
        return $this->percent;
    }

    public function setPercent(?float $percent): self
    {
        $this->percent = $percent;

        return $this;
    }

    public function isNotSellableFormWithMessage(): bool
    {
        return $this->notSellableFormWithMessage;
    }

    public function setNotSellableFormWithMessage(bool $notSellableFormWithMessage): self
    {
        $this->notSellableFormWithMessage = $notSellableFormWithMessage;

        return $this;
    }

    public function getProductType(): ?string
    {
        return $this->productType;
    }

    public function setProductType(?string $productType): self
    {
        $this->productType = $productType !== null ? \strtoupper($productType) : null;

        return $this;
    }

    public function getTarifId(): ?string
    {
        return $this->tarifId;
    }

    public function setTarifId(?string $tarifId): self
    {
        $this->tarifId = $tarifId;

        return $this;
    }

    public function getProductTopLabel(): ?string
    {
        return $this->productTopLabel;
    }

    public function setProductTopLabel(?string $productTopLabel): self
    {
        $this->productTopLabel = $productTopLabel;

        return $this;
    }

    public function getProductPricingPhrase(): ?string
    {
        return $this->productPricingPhrase;
    }

    public function setProductPricingPhrase(?string $productPricingPhrase): self
    {
        $this->productPricingPhrase = $productPricingPhrase;

        return $this;
    }

    public function getAccountAccordCadre(): ?AccountAccordCadre
    {
        return $this->accountAccordCadre;
    }

    public function setAccountAccordCadre(?AccountAccordCadre $accountAccordCadre): self
    {
        $this->accountAccordCadre = $accountAccordCadre;

        return $this;
    }

    public function getFavorites(): array
    {
        return $this->favorites;
    }

    public function setFavorites(array $favorites): self
    {
        $this->favorites = $favorites;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function isNewTarifNotification(): bool
    {
        return $this->newTarifNotification;
    }

    public function setNewTarifNotification(bool $newTarifNotification): self
    {
        $this->newTarifNotification = $newTarifNotification;

        return $this;
    }

    public function getMinOrderQuantity(): int
    {
        return $this->minOrderQuantity;
    }

    public function setMinOrderQuantity(int $minOrderQuantity): self
    {
        $this->minOrderQuantity = $minOrderQuantity;

        return $this;
    }

    public function getMaxOrderQuantity(): int
    {
        return $this->maxOrderQuantity;
    }

    public function setMaxOrderQuantity(int $maxOrderQuantity): self
    {
        $this->maxOrderQuantity = $maxOrderQuantity;

        return $this;
    }

    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function setAttachments(array $attachments): self
    {
        $this->attachments = $attachments;

        return $this;
    }

    public function getAccordCadreContent(): ?AccordCadreContent
    {
        return $this->accordCadreContent;
    }

    public function setAccordCadreContent(?AccordCadreContent $accordCadreContent): self
    {
        $this->accordCadreContent = $accordCadreContent;

        return $this;
    }

    public function getAccordId(): ?string
    {
        return $this->accordId;
    }

    public function setAccordId(?string $accordId): self
    {
        $this->accordId = $accordId;

        return $this;
    }

    public function getFranco(): ?float
    {
        return $this->franco;
    }

    public function setFranco(?float $franco): self
    {
        $this->franco = $franco;

        return $this;
    }

    public function getShippingCost(): ?float
    {
        return $this->shippingCost;
    }

    public function setShippingCost(?float $shippingCost): self
    {
        $this->shippingCost = $shippingCost;

        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(?string $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * @return array<int, array{quantity: int, price: float, priceReference: float}>
     */
    public function getPriceRanges(): array
    {
        return $this->priceRanges;
    }

    /**
     * @param array<int, array{quantity: int, price: float, priceReference: float}> $priceRanges
     */
    public function setPriceRanges(array $priceRanges): self
    {
        $this->priceRanges = $priceRanges;

        return $this;
    }

    public function getShippingCategory(): ?string
    {
        return $this->shippingCategory;
    }

    public function setShippingCategory(?string $shippingCategory): self
    {
        $this->shippingCategory = $shippingCategory;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function isFormWithMessageFat(): bool
    {
        return $this->formWithMessageFat;
    }

    public function setFormWithMessageFat(bool $formWithMessageFat): void
    {
        $this->formWithMessageFat = $formWithMessageFat;
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

    public function getEcoTax(): ?float
    {
        return $this->ecoTax;
    }

    public function setEcoTax(?float $ecoTax): self
    {
        $this->ecoTax = $ecoTax;

        return $this;
    }
}
