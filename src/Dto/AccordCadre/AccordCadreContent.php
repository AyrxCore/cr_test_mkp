<?php

declare(strict_types=1);

namespace App\Dto\AccordCadre;

use Symfony\Component\Serializer\Annotation\Groups;

final class AccordCadreContent
{
    #[Groups(['products:get', 'product:get'])]
    private string $tarifId;
    #[Groups(['products:get', 'product:get'])]
    private ?string $labelCtaRattachement = null;
    #[Groups(['products:get', 'product:get'])]
    private ?string $urlCtaRattachement = null;
    /** @var AccordCadreContentBlockInterface[] */
    #[Groups(['products:get', 'product:get'])]
    private array $listBlocks = [];
    #[Groups(['products:get', 'product:get'])]
    private string $name;
    #[Groups(['products:get', 'product:get'])]
    private string $type;
    #[Groups(['products:get', 'product:get'])]
    private ?string $labelNotActivated = null;
    #[Groups(['products:get', 'product:get'])]
    private ?string $labelPending = null;
    #[Groups(['products:get', 'product:get'])]
    private ?string $labelActivated = null;
    #[Groups(['product:get'])]
    private ?string $confirmationLayerDescription = null;
    #[Groups(['product:get'])]
    private ?string $confirmationLayerSuccess = null;
    #[Groups(['product:get'])]
    private bool $contactForm;

    public function getTarifId(): string
    {
        return $this->tarifId;
    }

    public function setTarifId(string $tarifId): self
    {
        $this->tarifId = $tarifId;

        return $this;
    }

    public function getLabelCtaRattachement(): ?string
    {
        return $this->labelCtaRattachement;
    }

    public function setLabelCtaRattachement(?string $labelCtaRattachement): self
    {
        $this->labelCtaRattachement = $labelCtaRattachement;

        return $this;
    }

    /**
     * @return array<string, AccordCadreContentBlockInterface>
     */
    public function getListBlocks(): array
    {
        return $this->listBlocks;
    }

    /**
     * @param array<string, AccordCadreContentBlockInterface> $listBlocks
     */
    public function setListBlocks(array $listBlocks): self
    {
        $this->listBlocks = [];
        foreach ($listBlocks as $block) {
            $this->addListBlock($block);
        }

        return $this;
    }

    public function addListBlock(AccordCadreContentBlockInterface $block): self
    {
        $componentName = $block->getComponentName();

        if ($componentName !== null && !\array_key_exists($componentName, $this->listBlocks)) {
            $this->listBlocks[$componentName] = $block;
        }

        return $this;
    }

    public function getUrlCtaRattachement(): ?string
    {
        return $this->urlCtaRattachement;
    }

    public function setUrlCtaRattachement(?string $urlCtaRattachement): self
    {
        $this->urlCtaRattachement = $urlCtaRattachement;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLabelNotActivated(): ?string
    {
        return $this->labelNotActivated;
    }

    public function setLabelNotActivated(?string $labelNotActivated): self
    {
        $this->labelNotActivated = $labelNotActivated;

        return $this;
    }

    public function getLabelPending(): ?string
    {
        return $this->labelPending;
    }

    public function setLabelPending(?string $labelPending): self
    {
        $this->labelPending = $labelPending;

        return $this;
    }

    public function getLabelActivated(): ?string
    {
        return $this->labelActivated;
    }

    public function setLabelActivated(?string $labelActivated): self
    {
        $this->labelActivated = $labelActivated;

        return $this;
    }

    public function isContactForm(): bool
    {
        return $this->contactForm;
    }

    public function setContactForm(bool $contactForm): self
    {
        $this->contactForm = $contactForm;

        return $this;
    }

    public function getConfirmationLayerDescription(): ?string
    {
        return $this->confirmationLayerDescription;
    }

    public function setConfirmationLayerDescription(?string $confirmationLayerDescription): self
    {
        $this->confirmationLayerDescription = $confirmationLayerDescription;

        return $this;
    }

    public function getConfirmationLayerSuccess(): ?string
    {
        return $this->confirmationLayerSuccess;
    }

    public function setConfirmationLayerSuccess(?string $confirmationLayerSuccess): self
    {
        $this->confirmationLayerSuccess = $confirmationLayerSuccess;

        return $this;
    }
}
