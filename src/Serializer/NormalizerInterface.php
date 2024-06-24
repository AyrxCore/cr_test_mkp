<?php

declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface as SymfonyNormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;

interface NormalizerInterface extends SymfonyNormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
    public const string EXTERNAL_API_CONTEXT_KEY = 'external_api_data';
}
