<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

final class Variant
{
    #[Groups(['products:get', 'product:get'])]
    private string $id;

    #[Groups(['products:get', 'product:get'])]
    private string $externalId;

    #[Groups(['products:get', 'product:get'])]
    private ?string $offerPriceExternalId = null;

    /**
     * @var array<string, mixed>
     */
    #[Groups(['products:get', 'product:get'])]
    private array $options = [];

    #[Groups(['products:get', 'product:get'])]
    private ?float $price = null;

    #[Groups(['products:get', 'product:get'])]
    private ?float $priceReference = null;

    #[Groups(['products:get', 'product:get'])]
    private float $percent = 0.0;

    /**
     * @var array<string>
     */
    #[Groups(['products:get', 'product:get'])]
    private array $images = [];

    /**
     * @var array<int, array{quantity: int, price: float, priceReference: float}>
     */
    #[Groups(['products:get', 'product:get'])]
    private array $priceRanges = [];

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getOfferPriceExternalId(): ?string
    {
        return $this->offerPriceExternalId;
    }

    public function setOfferPriceExternalId(?string $offerPriceExternalId): self
    {
        $this->offerPriceExternalId = $offerPriceExternalId;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPriceReference(): ?float
    {
        return $this->priceReference;
    }

    public function setPriceReference(?float $priceReference): self
    {
        $this->priceReference = $priceReference;

        return $this;
    }

    public function getPercent(): float
    {
        return $this->percent;
    }

    public function setPercent(float $percent): self
    {
        $this->percent = $percent;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @param array<string> $images
     */
    public function setImages(array $images): self
    {
        $this->images = $images;

        return $this;
    }

    public function getPriceRanges(): array
    {
        return $this->priceRanges;
    }

    public function setPriceRanges(array $priceRanges): self
    {
        $this->priceRanges = $priceRanges;

        return $this;
    }

    /**
     * Convertit le variant en tableau pour la sérialisation.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'externalId' => $this->externalId,
            'offerPriceExternalId' => $this->offerPriceExternalId,
            'options' => $this->options,
            'price' => $this->price,
            'priceReference' => $this->priceReference,
            'percent' => $this->percent,
            'images' => $this->images,
            'priceRanges' => $this->priceRanges,
        ];
    }
}
