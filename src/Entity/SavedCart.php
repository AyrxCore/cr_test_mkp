<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\Repository\SavedCartRepository;
use App\State\Processor\SavedCartPersistProcessor;
use App\State\Processor\SavedCartRemoveProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/saved-carts/{id}',
            normalizationContext: ['groups' => ['savedCart:get']]
        ),
        new Patch(
            uriTemplate: '/saved-carts/{id}',
            openapi: new Operation(
                summary: 'Modifier une liste de panier',
                description: 'Permet de mettre a jour le nom et la visibilité d\'une liste de panier sauvegardé'
            ),
            normalizationContext: ['groups' => ['update']],
            security: 'is_granted(\'MANAGE_SAVED_CART\', object)',
            validate: true
        ),
        new Delete(
            uriTemplate: '/saved-carts/{id}',
            openapi: new Operation(
                summary: 'Supprimer une liste de panier sauvegardé'
            ),
            security: 'is_granted(\'MANAGE_SAVED_CART\', object)',
            validate: true,
            processor: SavedCartRemoveProcessor::class
        ),
        new Get(
            uriTemplate: '/saved-carts/{id}/products',
            openapi: new Operation(
                summary: 'Afficher la liste des produits liés à un panier sauvegardé'
            )
        ),
        new GetCollection(
            uriTemplate: '/saved-carts'
        ),
        new Post(
            uriTemplate: '/saved-carts',
            openapi: new Operation(
                summary: 'Créer une nouvelle liste de panier sauvegardé',
                description: 'Permet de créer une nouvelle liste de panier sauvegardé'
            ),
            validate: true
        ),
    ],
    processor: SavedCartPersistProcessor::class
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
