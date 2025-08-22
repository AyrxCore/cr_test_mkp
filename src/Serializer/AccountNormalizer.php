<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Account;
use App\Service\UpplerBuyerCompanyService;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface as SfNormalizerInterface;

class AccountNormalizer extends AbstractNormalizer
{
    public function __construct(
        SfNormalizerInterface $normalizer,
        private readonly UpplerBuyerCompanyService $upplerBuyerCompanyService,
    ) {
        $this->normalizer = $normalizer;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Account && $this->normalizer->supportsNormalization($data, $format);
    }

    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return $data instanceof Account && $this->supportsDenormalization($data, $type, $format);
    }

    /**
     * @param Account $object
     * @throws \Exception
     */
    protected function getExternalApiData(mixed $object, array $context): ?\stdClass
    {
        if (!\in_array('account:external_api_data:buyer', $this->getSerializationGroups($context), true)) {
            return null;
        }

        return $this->upplerBuyerCompanyService->getBuyerByCompanyId($object->getUpplerCompanyId());
    }
}
