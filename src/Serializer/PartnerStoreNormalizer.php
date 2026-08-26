<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\PartnerStore;
use App\Helper\Formatter\PhoneFormatter;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface as SfNormalizerInterface;

class PartnerStoreNormalizer extends AbstractNormalizer
{
    public function __construct(
        protected SfNormalizerInterface $normalizer,
        private readonly PhoneFormatter $phoneFormatter,
    ) {
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof PartnerStore;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [PartnerStore::class => true];
    }

    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $data = parent::normalize($object, $format, $context);

        if (isset($data['phone'])) {
            $data['phone'] = $this->phoneFormatter->format($data['phone']);
        }

        return $data;
    }

    protected function getExternalApiData(mixed $object, array $context): ?\stdClass
    {
        return null;
    }
}
