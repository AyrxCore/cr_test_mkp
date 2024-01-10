<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ChannelParameterRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ChannelParameterRepository::class)]
class ChannelParameter
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['channel:get'])]
    private ?Uuid $id = null;

    #[ORM\OneToOne(inversedBy: 'channelParameter', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Channel $channel = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['channel:get'])]
    #[Assert\Url(
        message: 'A channel logo must be an URL',
    )]
    private ?string $logo = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['channel:get'])]
    #[Assert\Url(
        message: 'A channel favicon must be an URL',
    )]
    private ?string $favicon = null;

    #[ORM\Column(length: 7)]
    #[Groups(['channel:get'])]
    #[Assert\NotNull(
        message: 'A channel must have a primary color',
    )]
    #[Assert\Regex(
        pattern: '#[a-fA-F0-9]{6}',
        message: 'Invalid primary color'
    )]
    private ?string $primaryColor = null;

    #[ORM\Column(length: 7)]
    #[Groups(['channel:get'])]
    #[Assert\NotNull(
        message: 'A channel must have a secondary color',
    )]
    #[Assert\Regex(
        pattern: '#[a-fA-F0-9]{6}',
        message: 'Invalid secondary color'
    )]
    private ?string $secondaryColor = null;

    #[ORM\Column(length: 7)]
    #[Groups(['channel:get'])]
    #[Assert\NotNull(
        message: 'A channel must have a text color',
    )]
    #[Assert\Regex(
        pattern: '#[a-fA-F0-9]{6}',
        message: 'Invalid text color'
    )]
    private ?string $textColor = '#000000';

    #[ORM\Column(nullable: true)]
    #[Groups(['channel:get'])]
    #[Assert\Url]
    private ?string $privacyPolicy = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['channel:get'])]
    #[Assert\Url]
    private ?string $generalTermsOfUse = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['channel:get'])]
    #[Assert\Url]
    private ?string $legalTerms = null;

    #[ORM\Column]
    #[Groups(['channel:get'])]
    #[Assert\NotNull(
        message: 'A channel must have a phone number',
    )]
    #[Assert\Regex(
        pattern: '/^[\d\s+.-]+$/',
        message: 'Invalid phone number',
    )]
    private ?string $phoneNumber = null;

    #[ORM\Column]
    #[Groups(['channel:get'])]
    #[Assert\NotNull(
        message: 'An adherent service must have an email address',
    )]
    #[Assert\Email(
        message: 'Invalid adherent service email address',
    )]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['channel:get'])]
    private ?bool $whiteLabel = true;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getChannel(): ?Channel
    {
        return $this->channel;
    }

    public function setChannel(Channel $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getPrimaryColor(): ?string
    {
        return $this->primaryColor;
    }

    public function setPrimaryColor(string $primaryColor): self
    {
        $this->primaryColor = $primaryColor;

        return $this;
    }

    public function getSecondaryColor(): ?string
    {
        return $this->secondaryColor;
    }

    public function setSecondaryColor(string $secondaryColor): self
    {
        $this->secondaryColor = $secondaryColor;

        return $this;
    }

    public function getTextColor(): ?string
    {
        return $this->textColor;
    }

    public function setTextColor(?string $textColor): self
    {
        $this->textColor = $textColor;

        return $this;
    }

    public function getPrivacyPolicy(): ?string
    {
        return $this->privacyPolicy;
    }

    public function setPrivacyPolicy(?string $privacyPolicy): self
    {
        $this->privacyPolicy = $privacyPolicy;

        return $this;
    }

    public function getGeneralTermsOfUse(): ?string
    {
        return $this->generalTermsOfUse;
    }

    public function setGeneralTermsOfUse(?string $generalTermsOfUse): self
    {
        $this->generalTermsOfUse = $generalTermsOfUse;

        return $this;
    }

    public function getLegalTerms(): ?string
    {
        return $this->legalTerms;
    }

    public function setLegalTerms(?string $legalTerms): self
    {
        $this->legalTerms = $legalTerms;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getFavicon(): ?string
    {
        return $this->favicon;
    }

    public function setFavicon(string $favicon): self
    {
        $this->favicon = $favicon;

        return $this;
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

    public function isWhiteLabel(): ?bool
    {
        return $this->whiteLabel;
    }

    public function setWhiteLabel(bool $whiteLabel): self
    {
        $this->whiteLabel = $whiteLabel;

        return $this;
    }
}
