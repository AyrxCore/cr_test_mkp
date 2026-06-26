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

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Adherent && $this->normalizer->supportsNormalization($data, $format);
    }

    /**
     * @param Adherent $object
     */
    protected function getExternalApiData(mixed $object, array $context): ?\stdClass
    {
        return null;
    }
}
