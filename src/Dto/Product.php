<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Api\Buyer\ProductApiController;
use App\Dto\AccountAccordCadre;

final class Product implements \JsonSerializable
{

    public const PROCESS_STATUS_NOT_ACTIVATED = 'NOT_ACTIVATED';

    public const PROCESS_STATUS_PENDING = 'PENDING';

    public const PROCESS_STATUS_ACTIVATED = 'ACTIVATED';

    public const PROCESS_STATUS
        = [
            self::PROCESS_STATUS_NOT_ACTIVATED,
            self::PROCESS_STATUS_PENDING,
            self::PROCESS_STATUS_ACTIVATED,
        ];

    private ?int $id = null;

    private ?string $name;

    private ?string $reference;

    private ?string $slug;

    private ?string $description;

    private ?string $conditionnement;

    private array $livraisons;

    private array $categories;

    private ?int $imageId = null;

    private array $images;

    private array $options;

    private array $properties;

    private array $variants;

    private ?float $priceReference;

    private ?float $percent = 0;

    private ?float $price = null;

    private bool $isAccordCadre = false;

    private ?Price $basePrice = null;

    private ?Seller $seller;

    private ?AccountAccordCadre $accountAccordCadre;

    private array $favorites = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param  string|null  $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * @param  string|null  $reference
     */
    public function setReference(?string $reference): void
    {
        $this->reference = $reference;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string|null $slug
     */
    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }


    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param  string|null  $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return array
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param  array  $categories
     */
    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }

    /**
     * @return int|null
     */
    public function getImageId(): ?int
    {
        return $this->imageId;
    }

    /**
     * @param  int|null  $imageId
     */
    public function setImageId(?int $imageId): void
    {
        $this->imageId = $imageId;
    }


    /**
     * @return array
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @param  array  $images
     */
    public function setImages(array $images): void
    {
        $this->images = $images;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param  array  $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @return Property[]|array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param  Property[]|array  $properties
     */
    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    /**
     * @return array
     */
    public function getVariants(): array
    {
        return $this->variants;
    }

    /**
     * @param  array  $variants
     */
    public function setVariants(array $variants): void
    {
        $this->variants = $variants;
    }

    /**
     * @return float|null
     */
    public function getPriceReference(): ?float
    {
        return $this->priceReference;
    }

    /**
     * @param  float|null  $priceReference
     */
    public function setPriceReference(?float $priceReference): void
    {
        $this->priceReference = $priceReference;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param  float|null  $price
     */
    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    /**
     * @return Price|null
     */
    public function getBasePrice(): ?Price
    {
        return $this->basePrice;
    }

    /**
     * @param  Price|null  $basePrice
     */
    public function setBasePrice(?Price $basePrice): void
    {
        $this->basePrice = $basePrice;
    }

    /**
     * @return Seller|null
     */
    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    /**
     * @param  Seller|null  $seller
     */
    public function setSeller(?Seller $seller): void
    {
        $this->seller = $seller;
    }

    /**
     * @return string|null
     */
    public function getConditionnement(): ?string
    {
        return $this->conditionnement;
    }

    /**
     * @param  string|null  $conditionnement
     */
    public function setConditionnement(?string $conditionnement): void
    {
        $this->conditionnement = $conditionnement;
    }

    /**
     * @return array
     */
    public function getLivraisons(): array
    {
        return $this->livraisons;
    }

    /**
     * @param  array  $livraisons
     */
    public function setLivraisons(array $livraisons): void
    {
        $this->livraisons = $livraisons;
    }

    /**
     * @return float|null
     */
    public function getPercent(): ?float
    {
        return $this->percent;
    }

    /**
     * @param  float|null  $percent
     */
    public function setPercent(?float $percent): void
    {
        $this->percent = $percent;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * @return bool
     */
    public function isAccordCadre(): bool
    {
        return $this->isAccordCadre;
    }

    /**
     * @param  bool  $isAccordCadre
     */
    public function setIsAccordCadre(bool $isAccordCadre): void
    {
        $this->isAccordCadre = $isAccordCadre;
    }


    /**
     * @return AccountAccordCadre|null
     */
    public function getAccountAccordCadre(): ?AccountAccordCadre
    {
        return $this->accountAccordCadre;
    }

    /**
     * @param  AccountAccordCadre|null  $accountAccordCadre
     */
    public function setAccountAccordCadre(?AccountAccordCadre $accountAccordCadre): void
    {
        $this->accountAccordCadre = $accountAccordCadre;
    }

    /**
     * @return array
     */
    public function getFavorites(): array
    {
        return $this->favorites;
    }

    /**
     * @param array $favorites
     */
    public function setFavorites(array $favorites): void
    {
        $this->favorites = $favorites;
    }
}
