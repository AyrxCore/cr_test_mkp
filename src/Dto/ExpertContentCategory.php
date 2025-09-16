<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation;
use App\State\Provider\ExpertContentCategoryProvider;

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(
            uriTemplate: '/expert-content-categories',
            openapi: new Operation(
                summary: 'Liste des categories de contenus experts',
                description: 'Permet de récupérer les categories des contenus experts'
            )
        ),
    ],
    provider: ExpertContentCategoryProvider::class
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
