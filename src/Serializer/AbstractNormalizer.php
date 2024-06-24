<?php

declare(strict_types=1);

namespace App\Serializer;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface as SfNormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractNormalizer implements NormalizerInterface
{
    protected SfNormalizerInterface $normalizer;

    public function normalize($object, $format = null, array $context = []): float|array|\ArrayObject|bool|int|string|null
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        if (\is_array($data) && $externalApiData = $this->getExternalApiData($object, $context)) {
            $externalApiDataKey = $this->getInflector()->camelize(NormalizerInterface::EXTERNAL_API_CONTEXT_KEY);

            $data[$externalApiDataKey] = \json_decode(\json_encode($externalApiData), true);
        }

        return $data;
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        return $this->denormalize($data, $type, $format, $context);
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        if ($this->normalizer instanceof SerializerAwareInterface) {
            $this->normalizer->setSerializer($serializer);
        }
    }

    protected function getInflector(): Inflector
    {
        return InflectorFactory::create()->build();
    }

    protected function getSerializationGroups(array $context): array
    {
        return (array) ($context['groups'] ?? []);
    }

    abstract protected function getExternalApiData(mixed $object, array $context): ?\stdClass;
}
