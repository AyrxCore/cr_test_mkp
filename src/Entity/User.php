<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Repository\UserRepository;
use App\State\Provider\UserMeItemProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Uid\Uuid;

#[ApiResource(
    operations: [
        new Get(
            controller: NotFoundAction::class,
            output: false,
            read: false
        ),
        new Get(
            uriTemplate: '/me',
            defaults: ['id' => '7de7d979-b89a-4ea7-bb98-2772cf91fa84'],
            openapiContext: ['summary' => 'Get current user info', 'description' => 'Get current user info'],
            normalizationContext: ['groups' => ['user:simple', 'user:me', 'user:external_api_data:subaccount', 'user:external_api_data:buyer']],
            provider: UserMeItemProvider::class
        )]
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['account:get'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['account:get', 'user:simple'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups('user:simple')]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['account:get', 'user:simple'])]
    private ?string $username = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['account:get', 'user:simple'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['account:get', 'user:simple'])]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastLogin = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Account::class)]
    private Collection $accounts;

    #[SerializedName('account')]
    #[Groups(['user:me'])]
    private ?Account $currentAccount = null;

    #[ORM\Column]
    #[Groups(['account:get', 'user:simple'])]
    private ?bool $enabled = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $confirmation_token = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $password_requested_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $first_connexion_requested_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailChangingToken = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $emailChangingRequestedAt = null;

    #[ORM\OneToMany(mappedBy: '_user', targetEntity: UserInfoUpdateRequest::class)]
    #[Groups(['user:simple'])]
    private Collection $userInfoUpdateRequests;

    public function __construct()
    {
        $this->accounts = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->updatedAt = new \DateTime('now');
        $this->createdAt = new \DateTime('now');
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTime('now');
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return \array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole($role): static
    {
        $role = \strtoupper($role);

        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole($role): static
    {
        $role = \strtoupper($role);

        $index = \array_search($role, $this->roles, true);
        if ($index >= 0) {
            unset($this->roles[$index]);
        }

        return $this;
    }

    public function hasRole($role): bool
    {
        return \in_array(\strtoupper($role), $this->getRoles(), true);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Account>
     */
    public function getAccounts(): Collection
    {
        return $this->accounts;
    }

    public function addAccount(Account $account): self
    {
        if (!$this->accounts->contains($account)) {
            $this->accounts->add($account);
            $account->setUser($this);
        }

        return $this;
    }

    public function removeAccount(Account $account): self
    {
        if ($this->accounts->removeElement($account)) {
            // set the owning side to null (unless already changed)
            if ($account->getUser() === $this) {
                $account->setUser(null);
            }
        }

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmation_token;
    }

    public function setConfirmationToken(?string $confirmation_token): self
    {
        $this->confirmation_token = $confirmation_token;

        return $this;
    }

    public function getPasswordRequestedAt(): ?\DateTimeInterface
    {
        return $this->password_requested_at;
    }

    public function setPasswordRequestedAt(?\DateTimeInterface $password_requested_at): self
    {
        $this->password_requested_at = $password_requested_at;

        return $this;
    }

    public function getFirstConnexionRequestedAt(): ?\DateTimeInterface
    {
        return $this->first_connexion_requested_at;
    }

    public function setFirstConnexionRequestedAt(?\DateTimeInterface $first_connexion_requested_at): self
    {
        $this->first_connexion_requested_at = $first_connexion_requested_at;

        return $this;
    }

    public function getFirstEnabledAccount(Channel $channel = null): ?Account
    {
        /** @var Account $account */
        foreach ($this->accounts as $account) {
            if ((!$channel || $account->getAdherent()?->getChannel() === $channel) && $account->isEnabled()) {
                return $account;
            }
        }

        return null;
    }

    public function getEmailChangingToken(): ?string
    {
        return $this->emailChangingToken;
    }

    public function setEmailChangingToken(?string $emailChangingToken): self
    {
        $this->emailChangingToken = $emailChangingToken;

        return $this;
    }

    public function getEmailChangingRequestedAt(): ?\DateTimeInterface
    {
        return $this->emailChangingRequestedAt;
    }

    public function setEmailChangingRequestedAt(?\DateTimeInterface $emailChangingRequestedAt): self
    {
        $this->emailChangingRequestedAt = $emailChangingRequestedAt;

        return $this;
    }

    public function getUserInfoUpdateRequests(): Collection
    {
        return $this->userInfoUpdateRequests;
    }

    public function setUserInfoUpdateRequests(Collection $userInfoUpdateRequests): void
    {
        $this->userInfoUpdateRequests = $userInfoUpdateRequests;
    }

    public function getCurrentAccount(): ?Account
    {
        return $this->currentAccount;
    }

    public function setCurrentAccount(?Account $currentAccount): void
    {
        $this->currentAccount = $currentAccount;
    }
}
