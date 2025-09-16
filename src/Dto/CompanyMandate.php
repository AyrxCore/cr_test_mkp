<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation;
use App\State\Provider\CompanyMandateProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/company/mandates',
            openapi: new Operation(
                summary: 'Liste des mandats existants pour la company de l\'utilisateur'
            )
        ),
    ],
    provider: CompanyMandateProvider::class
)]
final class CompanyMandate
{
    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    #[Assert\Type('string', message: '(string) Iban')]
    private ?string $iban = null;

    #[Assert\Type('string', message: '(string) Created at')]
    private ?string $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getIban(): ?string
    {
        return $this->iban;
    }

    public function setIban(string $iban): ?self
    {
        $this->iban = $iban;

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): ?self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
