<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model\Operation;
use App\State\Provider\InvoiceProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/invoices/{id}/download',
            openapi: new Operation(
                summary: 'Télécharger la facture de la commande',
                description: 'Cette opération permet de télécharger le fichier pdf  d\'une commande en spécifiant l\'identifiant de la facture.'
            )),
    ],
    provider: InvoiceProvider::class
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
