<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Api\Buyer\AccordCadreApiController;

#[ApiResource(
    collectionOperations: [
        'search' => [
            "openapi_context" => [
                'summary' => 'Liste des FAT',
                'description' => 'Permet de récupérer la liste des produits de type accord cadre avec les paramètres de propriétés'
            ],
            'path' => '/accords-cadre',
            'controller' => AccordCadreApiController::class,
            'method' => 'POST'
        ]
    ],
    itemOperations: [
        'get' => [
            'path' => '/accord-cadre/{id}',
            'requirements' => ['id' => '\d+']
        ],
    ]
)]
final class AccordCadre implements \JsonSerializable
{
    public const PROCESS_STATUS_NOT_ACTIVATED = 'NOT_ACTIVATED';
    public const PROCESS_STATUS_PENDING = 'PENDING';
    public const PROCESS_STATUS_ACTIVATED = 'ACTIVATED';
    public const PROCESS_STATUS = [
        self::PROCESS_STATUS_NOT_ACTIVATED,
        self::PROCESS_STATUS_PENDING,
        self::PROCESS_STATUS_ACTIVATED,
    ];
    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    private ?string $name;
    private ?string $reference;
    private ?string $description;
    private array $properties;
    private array $categories;

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
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     */
    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
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


    public function jsonSerialize()
    {
        return  get_object_vars($this);
    }
}
