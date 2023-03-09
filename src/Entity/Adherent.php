<?php

namespace App\Entity;

use App\Repository\AdherentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: AdherentRepository::class)]
class Adherent
{

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'adherent', targetEntity: AccordStatut::class, orphanRemoval: true)]
    private Collection $accordStatuts;

    #[ORM\OneToMany(mappedBy: 'adherent', targetEntity: Account::class)]
    private Collection $accounts;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["simpleUser"])]
    private ?string $reducceCode = null;


    public function __construct()
    {
        $this->accordStatuts = new ArrayCollection();
        $this->accounts = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    /**
     * @param  Uuid|null  $id
     */
    public function setId(?Uuid $id): void
    {
        $this->id = $id;
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
     * @return Collection<int, AccordStatut>
     */
    public function getAccordStatuts(): Collection
    {
        return $this->accordStatuts;
    }

    public function setAccordStatuts(Collection $accordStatuts): void
    {
        $this->accordStatuts = $accordStatuts;
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
            $account->setAdherent($this);
        }

        return $this;
    }

    public function removeAccount(Account $account): self
    {
        if ($this->accounts->removeElement($account)) {
            // set the owning side to null (unless already changed)
            if ($account->getAdherent() === $this) {
                $account->setAdherent(null);
            }
        }

        return $this;
    }

    public function getReducceCode(): ?string
    {
        return $this->reducceCode;
    }

    public function setReducceCode(?string $reducceCode): self
    {
        $this->reducceCode = $reducceCode;

        return $this;
    }


}
