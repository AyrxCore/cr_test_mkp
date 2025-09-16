<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation;
use App\State\Provider\CountryProvider;

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(
            uriTemplate: '/countries',
            openapi: new Operation(
                summary: 'Liste des pays',
                description: 'Permet de récupérer la liste des pays paginée et filtrable'
            ),
        ),
    ],
    provider: CountryProvider::class
)]
final class Country
{
    #[ApiProperty(identifier: true)]
    private int $id;

    private string $name;

    public function getId(): int
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

    public function setName(string $name): ?self
    {
        $this->name = $name;

        return $this;
    }
}
