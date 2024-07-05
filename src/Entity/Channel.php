<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Repository\ChannelRepository;
use App\State\Provider\ChannelProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            controller: NotFoundAction::class,
            output: false,
            read: false
        ),
        new Get(
            uriTemplate: '/channels/by-host/{hostname}',
            outputFormats: 'json',
            uriVariables: 'hostname',
            openapiContext: ['summary' => 'Retrieves a Channel by its hostname', 'parameters' => [['name' => 'hostname', 'in' => 'path', 'required' => true, 'type' => 'string', 'description' => 'The hostname of the Channel']]],
            normalizationContext: ['groups' => ['channel:get']],
            provider: ChannelProvider::class
        ),
    ]
)]
#[ORM\Entity(repositoryClass: ChannelRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(
    name: 'code_hostname_unique_idx',
    columns: ['code', 'hostname'],
)]
class Channel
{
    public const DLR = 'DLR';
    public const QANTIS_ACHAT = 'QANTIS_ACHAT';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['channel:get'])]
    private ?Uuid $id = null;

    #[ORM\Column]
    #[Groups(['channel:get'])]
    private ?string $name = null;

    #[ORM\Column(length: 20, unique: true)]
    #[Groups(['channel:get'])]
    #[Assert\NotNull(
        message: 'A channel code must be set',
    )]
    #[Assert\Regex(
        pattern: '/^[A-Z_]+$/',
        message: 'A channel code can only contain underscores and uppercase letters'
    )]
    #[ApiProperty(
        description: 'Unique code of the channel',
        example: 'QANTIS_MARKETPLACE',
        openapiContext: [
            'example' => 'QANTIS_MARKETPLACE',
        ],
    )]
    private ?string $code = null;

    #[ORM\Column(length: 128, unique: true)]
    #[Groups(['channel:get'])]
    #[Assert\NotNull(
        message: 'A channel must have a hostname',
    )]
    #[Assert\Regex(
        pattern: '/^https?/',
        message: 'A channel hostname should not have protocol',
        match: false
    )]
    private ?string $hostname = null;

    #[ORM\OneToOne(mappedBy: 'channel', cascade: ['persist', 'remove'])]
    #[Groups(['channel:get'])]
    private ?ChannelParameter $channelParameter = null;

    #[ORM\OneToMany(mappedBy: 'channel', targetEntity: Adherent::class)]
    private Collection $adherents;

    #[ORM\OneToMany(mappedBy: 'channel', targetEntity: ChannelOption::class, orphanRemoval: true)]
    #[Groups(['channel:get'])]
    private Collection $channelOptions;

    public function __construct()
    {
        $this->adherents = new ArrayCollection();
        $this->channelOptions = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    public function setHostname(?string $hostname): void
    {
        $this->hostname = $hostname;
    }

    public function getChannelParameter(): ?ChannelParameter
    {
        return $this->channelParameter;
    }

    public function setChannelParameter(ChannelParameter $channelParameter): self
    {
        // set the owning side of the relation if necessary
        if ($channelParameter->getChannel() !== $this) {
            $channelParameter->setChannel($this);
        }

        $this->channelParameter = $channelParameter;

        return $this;
    }

    /**
     * @return Collection<int, Adherent>
     */
    public function getAdherents(): Collection
    {
        return $this->adherents;
    }

    public function addAdherent(Adherent $adherent): self
    {
        if (!$this->adherents->contains($adherent)) {
            $this->adherents->add($adherent);
            $adherent->setChannel($this);
        }

        return $this;
    }

    public function removeAdherent(Adherent $adherent): self
    {
        if ($this->adherents->removeElement($adherent)) {
            // set the owning side to null (unless already changed)
            if ($adherent->getChannel() === $this) {
                $adherent->setChannel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ChannelOption>
     */
    public function getChannelOptions(): Collection
    {
        return $this->channelOptions;
    }

    public function addChannelOption(ChannelOption $channelOption): self
    {
        if (!$this->channelOptions->contains($channelOption)) {
            $this->channelOptions->add($channelOption);
            $channelOption->setChannel($this);
        }

        return $this;
    }

    public function removeChannelOption(ChannelOption $channelOption): self
    {
        if ($this->channelOptions->removeElement($channelOption)) {
            // set the owning side to null (unless already changed)
            if ($channelOption->getChannel() === $this) {
                $channelOption->setChannel(null);
            }
        }

        return $this;
    }

    public function getChannelOptionValueByKey(string $key): ?string
    {
        foreach ($this->channelOptions as $channelOption) {
            if ($channelOption->getName() === $key) {
                return $channelOption->getValue();
            }
        }

        return null;
    }
}
