<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\SavedCartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    collectionOperations: [
        'get' => [
            'method' => 'GET',
            'path' => '/saved-carts',
        ],
        'create' => [
            'openapi_context' => [
                'summary' => 'Créer une nouvelle liste de panier sauvegardé',
                'description' => 'Permet de créer une nouvelle liste de panier sauvegardé',
            ],
            'method' => 'POST',
            'validate' => true,
            'path' => '/saved-carts',
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['savedCart:get']],
            'method' => 'GET',
            'path' => '/saved-carts/{id}',
        ],
        'patch' => [
            'openapi_context' => [
                'summary' => 'Modifier une liste de panier',
                'description' => "Permet de mettre a jour le nom et la visibilité d'une liste de panier sauvegardé",
            ],
            'security' => "is_granted('MANAGE_SAVED_CART', object)",
            'method' => 'PATCH',
            'path' => '/saved-carts/{id}',
            'validate' => true,
            'normalization_context' => ['groups' => ['update']],
        ],
        'delete' => [
            'openapi_context' => [
                'summary' => 'Supprimer une liste de panier sauvegardé',
            ],
            'security' => "is_granted('MANAGE_SAVED_CART', object)",
            'method' => 'DELETE',
            'path' => '/saved-carts/{id}',
            'validate' => true,
        ],
        'get_uppler_products' => [
            'method' => 'GET',
            'path' => '/saved-carts/{id}/products',
            'openapi_context' => [
                'summary' => 'Afficher la liste des produits liés à un panier sauvegardé',
            ],
        ],
    ],
)]
#[ORM\Entity(repositoryClass: SavedCartRepository::class)]
#[ORM\UniqueConstraint(
    name: 'saved_cart_name_account_unique_idx',
    columns: ['name', 'account_id'],
)]
#[UniqueEntity(
    fields: ['name', 'account'],
    message: 'Ce libellé {{ value }} est déjà utilisé'
)]
#[ORM\Table(name: '`saved_cart`')]
#[ORM\HasLifecycleCallbacks]
class SavedCart
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['savedCart:get'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['update', 'savedCart:get'])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'savedCarts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    #[ORM\Column]
    #[Groups(['savedCart:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['update', 'savedCart:get'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'savedCart', targetEntity: SavedCartProduct::class, cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['savedCart:get'])]
    #[ApiSubresource]
    private Collection $savedCartProducts;

    public function __construct()
    {
        $this->savedCartProducts = new ArrayCollection();
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

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

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

    /**
     * @return Collection<int, SavedCartProduct>
     */
    public function getSavedCartProducts(): Collection
    {
        return $this->savedCartProducts;
    }

    public function addSavedCartProduct(SavedCartProduct $savedCartProduct): self
    {
        if (!$this->savedCartProducts->contains($savedCartProduct)) {
            $this->savedCartProducts->add($savedCartProduct);
            $savedCartProduct->setSavedCart($this);
        }

        return $this;
    }

    public function removeSavedCartProduct(SavedCartProduct $savedCartProduct): self
    {
        if ($this->savedCartProducts->removeElement($savedCartProduct)) {
            // set the owning side to null (unless already changed)
            if ($savedCartProduct->getSavedCart() === $this) {
                $savedCartProduct->setSavedCart(null);
            }
        }

        return $this;
    }
}
