<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Scalar;
use Symfony\Component\Validator\Constraints as Assert;
#[ApiResource(
    collectionOperations: [
        'get' => [
            "openapi_context" => [
                'summary' => 'Liste des pays',
                'description' => 'Permet de récupérer la liste des pays paginée et filtrable'
            ],
            "method" => "GET",
            "validate" => true,
        ]
    ],
    itemOperations: [
        'get'
    ]
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
