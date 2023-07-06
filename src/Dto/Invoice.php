<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;

#[ApiResource(
    collectionOperations: [],
    itemOperations: [
        'get' => [
            'openapi_context' => [
                'summary' => "Télécharger la facture de la commande",
                'description' => "Cette opération permet de télécharger le fichier pdf  d'une commande en spécifiant l'identifiant de la facture."
            ],
            'path' => '/invoices/{id}/download',
        ],
    ]
)]
final class Invoice
{
    #[ApiProperty(identifier: true)]
    private ?int $id;
    private string $name;
    private string $content;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}
