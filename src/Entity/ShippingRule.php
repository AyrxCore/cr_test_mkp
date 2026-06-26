<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ShippingRuleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ShippingRuleRepository::class)]
#[ApiResource]
class ShippingRule
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'shippingRules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Partner $partner = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: TYPES::JSONB)]
    private array $rule = [];

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    public function setPartner(?Partner $partner): self
    {
        $this->partner = $partner;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getRule(): array
    {
        return $this->rule;
    }

    public function setRule(array $rule): self
    {
        $this->rule = $rule;

        return $this;
    }
}
