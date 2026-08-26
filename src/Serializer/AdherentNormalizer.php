<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Adherent;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface as SfNormalizerInterface;

class AdherentNormalizer extends AbstractNormalizer
{
    public function __construct(
        SfNormalizerInterface $normalizer,
    ) {
        $this->normalizer = $normalizer;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Adherent && $this->normalizer->supportsNormalization($data, $format);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [Adherent::class => false];
    }

    /**
     * @param Adherent $object
     */
    protected function getExternalApiData(mixed $object, array $context): ?\stdClass
    {
        return null;
    }
}
