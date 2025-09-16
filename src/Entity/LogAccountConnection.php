<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LogAccountConnectionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogAccountConnectionRepository::class)]
class LogAccountConnection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $connectedAt = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getConnectedAt(): ?\DateTimeImmutable
    {
        return $this->connectedAt;
    }

    public function setConnectedAt(\DateTimeImmutable $connectedAt): self
    {
        $this->connectedAt = $connectedAt;

        return $this;
    }
}
