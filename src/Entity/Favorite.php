<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Api\Buyer\FavoriteApiController;
use App\Repository\FavoriteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    collectionOperations: [
        'get',
        'create' => [
            "openapi_context" => [
                'summary' => 'Créer une nouvelle liste de favori',
                'description' => 'Permet de créer une nouvelle liste de favori'
            ],
            "method" => "POST",
            "validate" => true,
            "path" => "/favorites/create"
        ]
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['favorite:get']],
        ],
        'update' => [
            "openapi_context"        => [
                'summary'     => 'Modifier une liste de favori',
                'description' => "Permet de mettre a jour le nom et la visibilité d'une liste de favoris",
            ],
            "method"                 => "PATCH",
            "validate"               => true,
            "path" => "/favorites/update/{id}",
            'normalization_context' => ['groups' => ['update']],
        ],
        'delete' => [
            'openapi_context' => [
                'summary' => 'Supprimer une liste de favori',
            ],
            'method' => 'DELETE',
            'validate' => true,
            "path" => "/favorites/delete/{id}",
        ],
        'get_upplers_products' => [
          "method" => "GET",
          "path" => "/favorites/{id}/products",
          "controller" => FavoriteApiController::class,
          "openapi_context" =>  [
              'summary' => 'Afficher la liste des produits liés à un favori'
          ],
        ]
    ]
)]
#[ORM\Entity(repositoryClass: FavoriteRepository::class)]
#[ORM\UniqueConstraint(
    name: 'name_account_unique_idx',
    columns: ['name', 'account_id'],
)]
#[UniqueEntity(
    fields: ['name', 'account'],
    message: 'Ce libellé {{ value }} est déjà utilisé'
)]
#[ORM\HasLifecycleCallbacks]
class Favorite
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['favorite:get'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['update', 'favorite:get'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['favorite:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['update', 'favorite:get'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'favorites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    #[ORM\Column]
    #[Groups(['update', 'favorite:get'])]
    private ?bool $public = true;

    #[ORM\OneToMany(mappedBy: 'favorite', targetEntity: FavoriteProduct::class, cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['favorite:get'])]
    private Collection $favoriteProducts;

    public function __construct()
    {
        $this->favoriteProducts = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->updatedAt = new \DateTimeImmutable('now');
        $this->createdAt = new \DateTimeImmutable('now');
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable('now');
    }

    /**
     * @return Uuid|null
     */
    public function getId(): ?Uuid
    {
        return $this->id;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function isPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): self
    {
        $this->public = $public;

        return $this;
    }

    /**
     * @return Collection<int, FavoriteProduct>
     */
    public function getFavoriteProducts(): Collection
    {
        return $this->favoriteProducts;
    }

    public function addFavoriteProduct(FavoriteProduct $favoriteProduct): self
    {
        if (!$this->favoriteProducts->contains($favoriteProduct)) {
            $this->favoriteProducts->add($favoriteProduct);
            $favoriteProduct->setFavorite($this);
        }

        return $this;
    }

    public function removeFavoriteProduct(FavoriteProduct $favoriteProduct): self
    {
        if ($this->favoriteProducts->removeElement($favoriteProduct)) {
            // set the owning side to null (unless already changed)
            if ($favoriteProduct->getFavorite() === $this) {
                $favoriteProduct->setFavorite(null);
            }
        }

        return $this;
    }
}
