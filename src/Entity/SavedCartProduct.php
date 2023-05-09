<?php

namespace App\Entity;

use App\Repository\SavedCartProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SavedCartProductRepository::class)]
class SavedCartProduct
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['savedCart:get'])]
    private ?Uuid $id = null;

    #[ORM\Column]
    #[Groups(['savedCart:get'])]
    private ?int $upplerProductId = null;

    #[ORM\Column]
    #[Groups(['savedCart:get'])]
    private ?int $upplerVariantId = null;

    #[ORM\Column(length: 255)]
    #[Groups(['savedCart:get'])]
    private ?string $UpplerProductName = null;

    #[ORM\Column]
    #[Groups(['savedCart:get'])]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'savedCartProducts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SavedCart $savedCart = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getUpplerProductId(): ?int
    {
        return $this->upplerProductId;
    }

    public function setUpplerProductId(int $upplerProductId): self
    {
        $this->upplerProductId = $upplerProductId;

        return $this;
    }

    public function getUpplerVariantId(): ?int
    {
        return $this->upplerVariantId;
    }

    public function setUpplerVariantId(int $upplerVariantId): self
    {
        $this->upplerVariantId = $upplerVariantId;

        return $this;
    }

    public function getUpplerProductName(): ?string
    {
        return $this->UpplerProductName;
    }

    public function setUpplerProductName(string $UpplerProductName): self
    {
        $this->UpplerProductName = $UpplerProductName;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getSavedCart(): ?SavedCart
    {
        return $this->savedCart;
    }

    public function setSavedCart(?SavedCart $savedCart): self
    {
        $this->savedCart = $savedCart;

        return $this;
    }
}
