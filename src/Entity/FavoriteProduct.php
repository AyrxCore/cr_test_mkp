<?php

namespace App\Entity;

use App\Repository\FavoriteProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: FavoriteProductRepository::class)]
class FavoriteProduct
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['favorite:get'])]
    private ?Uuid $id = null;

    #[ORM\Column]
    #[Groups(['favorite:get'])]
    private ?int $upplerProductId = null;

    #[ORM\Column]
    #[Groups(['favorite:get'])]
    private ?int $upplerVariantId = null;

    #[ORM\Column(length: 255)]
    #[Groups(['favorite:get'])]
    private ?string $upplerProductName = null;

    #[ORM\ManyToOne(inversedBy: 'favoriteProducts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Favorite $favorite = null;

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
        return $this->upplerProductName;
    }

    public function setUpplerProductName(string $upplerProductName): self
    {
        $this->upplerProductName = $upplerProductName;

        return $this;
    }

    public function getFavorite(): ?Favorite
    {
        return $this->favorite;
    }

    public function setFavorite(?Favorite $favorite): self
    {
        $this->favorite = $favorite;

        return $this;
    }
}
