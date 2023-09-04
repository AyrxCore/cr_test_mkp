<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;

#[ApiResource(
    collectionOperations: [
        'get' => [
            'openapi_context' => [
                'summary' => 'Liste des categories de contenus experts',
                'description' => 'Permet de récupérer les categories des contenus experts',
            ],
            'path' => '/expert-content-categories',
            'method' => 'GET',
        ],
    ],
    itemOperations: [
        'get',
    ],
)]
class ExpertContentCategory
{
    #[ApiProperty(identifier: true)]
    private ?int $id = null;
    private string $name = '';
    private string $color = '';

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }
}
