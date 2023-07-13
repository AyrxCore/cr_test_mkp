<?php

declare(strict_types=1);

namespace App\Dto;

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

    public const HOME_TOP_VENTE = 'home-top-vente';
    public const HOME_SELECTION = 'home-selection';

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

    public function getImageId(): ?int
    {
        return $this->imageId;
    }

    public function setImageId(?int $imageId): void
    {
        $this->imageId = $imageId;
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

    public function getBasePrice(): ?Price
    {
        return $this->basePrice;
    }

    public function setBasePrice(?Price $basePrice): void
    {
        $this->basePrice = $basePrice;
    }

    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    public function setSeller(?Seller $seller): void
    {
        $this->seller = $seller;
    }

    public function getConditionnement(): ?string
    {
        return $this->conditionnement;
    }

    public function setConditionnement(?string $conditionnement): void
    {
        $this->conditionnement = $conditionnement;
    }

    public function getLivraisons(): array
    {
        return $this->livraisons;
    }

    public function setLivraisons(array $livraisons): void
    {
        $this->livraisons = $livraisons;
    }

    public function getPercent(): ?float
    {
        return $this->percent;
    }

    public function setPercent(?float $percent): void
    {
        $this->percent = $percent;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function isAccordCadre(): bool
    {
        return $this->isAccordCadre;
    }

    public function setIsAccordCadre(bool $isAccordCadre): void
    {
        $this->isAccordCadre = $isAccordCadre;
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
}
