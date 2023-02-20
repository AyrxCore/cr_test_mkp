<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Api\Buyer\ProductApiController;

#[ApiResource(
    collectionOperations: [
        'search_products' => [
            "openapi_context" => [
                'summary' => 'Liste des produits',
                'description' => 'Permet de récupérer la liste des produits avec les paramètres de propriétés et de catégorie'
            ],
            'path' => '/products',
            'controller' => ProductApiController::class,
            'method' => 'post'
        ]
    ],
    itemOperations: [
        'get' => [
            'path' => '/product/{id}',
            'requirements' => ['id' => '\d+']
        ],
    ]
)]
final class Product implements \JsonSerializable
{
    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    private ?string $name;
    private ?string $reference;
    private ?string $description;
    private ?string $conditionnement;
    private array $livraisons;
    private array $categories;
    private array $images;
    private array $options;
    private array $properties;
    private array $variants;
    private ?float $priceReference;
    private ?float $percent = 0;
    private ?Price $price = null;
    private ?Price $basePrice = null;
    private ?Seller $seller;

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
     * @param string|null $name
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
     * @param string|null $reference
     */
    public function setReference(?string $reference): void
    {
        $this->reference = $reference;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
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
     * @param array $categories
     */
    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }

    /**
     * @return array
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @param array $images
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
     * @param array $options
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
     * @param Property[]|array $properties
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
     * @param array $variants
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
     * @param float|null $priceReference
     */
    public function setPriceReference(?float $priceReference): void
    {
        $this->priceReference = $priceReference;
    }

    /**
     * @return Price|null
     */
    public function getPrice(): ?Price
    {
        return $this->price;
    }

    /**
     * @param Price|null $price
     */
    public function setPrice(?Price $price): void
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
     * @param Price|null $basePrice
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
     * @param Seller|null $seller
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
     * @param string|null $conditionnement
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
     * @param array $livraisons
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
     * @param float|null $percent
     */
    public function setPercent(?float $percent): void
    {
        $this->percent = $percent;
    }

    public function jsonSerialize()
    {
        return  get_object_vars($this);
    }

}
