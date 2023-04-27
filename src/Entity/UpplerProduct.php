<?php

namespace App\Entity;

use App\Repository\UpplerProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UpplerProductRepository::class)]
#[UniqueEntity(fields: ['upplerProductId', 'upplerVariantId'], message: 'Cette combinaison de produit a déjà été ajouté à la liste')]

class UpplerProduct
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
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Favorite::class, mappedBy: 'upplerProducts')]
    private Collection $favorites;

    public function __construct()
    {
        $this->favorites = new ArrayCollection();
    }

    /**
     * @return Uuid|null
     */
    public function getId(): ?Uuid
    {
        return $this->id;
    }

    /**
     * @param Uuid|null $id
     */
    public function setId(?Uuid $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int|null
     */
    public function getUpplerProductId(): ?int
    {
        return $this->upplerProductId;
    }

    /**
     * @param int|null $upplerProductId
     */
    public function setUpplerProductId(?int $upplerProductId): void
    {
        $this->upplerProductId = $upplerProductId;
    }

    /**
     * @return int|null
     */
    public function getUpplerVariantId(): ?int
    {
        return $this->upplerVariantId;
    }

    /**
     * @param int|null $upplerVariantId
     */
    public function setUpplerVariantId(?int $upplerVariantId): void
    {
        $this->upplerVariantId = $upplerVariantId;
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Favorite>
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Favorite $favorite): self
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites->add($favorite);
            $favorite->addUpplerProduct($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): self
    {
        if ($this->favorites->removeElement($favorite)) {
            $favorite->removeUpplerProduct($this);
        }

        return $this;
    }
}
