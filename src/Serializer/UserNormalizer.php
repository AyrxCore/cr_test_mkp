<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\User;
use App\Repository\AccountRepository;
use App\Service\UpplerAccountService;
use App\Service\UpplerBuyerCompanyService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface as SfNormalizerInterface;

class UserNormalizer extends AbstractNormalizer
{
    public function __construct(
        SfNormalizerInterface $normalizer,
        private readonly UpplerAccountService $upplerAccountService,
        private readonly UpplerBuyerCompanyService $upplerBuyerCompanyService,
        private readonly RequestStack $requestStack,
        private readonly AccountRepository $accountRepository,
    ) {
        $this->normalizer = $normalizer;
    }

    /**
     * @param User $object
     *
     * @throws ExceptionInterface
     */
    public function normalize($object, $format = null, array $context = []): float|int|bool|\ArrayObject|array|string|null
    {
        $serializationGroups = $this->getSerializationGroups($context);

        if (
            \in_array('user:me', $serializationGroups, true)
            && $sessionAccount = $this->requestStack->getSession()->get('account')
        ) {
            $account = $this->accountRepository->find($sessionAccount->getId());
            $object->setCurrentAccount($account);
        }

        return parent::normalize($object, $format, $context);
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof User && $this->normalizer->supportsNormalization($data, $format);
    }

    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return $data instanceof User && $this->supportsDenormalization($data, $type, $format);
    }

    /**
     * @param User $object
     */
    protected function getExternalApiData(mixed $object, array $context): ?\stdClass
    {
        $serializationGroups = $this->getSerializationGroups($context);

        if (
            !\in_array('user:external_api_data:subaccount', $serializationGroups, true)
            && !\in_array('user:external_api_data:buyer', $serializationGroups, true)
        ) {
            return null;
        }

        $data = [
            'subaccount' => null,
            'buyer' => null,
        ];

        if (\in_array('user:external_api_data:subaccount', $serializationGroups, true)) {
            $data['subaccount'] = $this->upplerAccountService->getUserSubAccountData();
        }

        if (\in_array('user:external_api_data:buyer', $serializationGroups, true)) {
            $data['buyer'] = $this->upplerBuyerCompanyService->getUserBuyerData();
        }

        if (!isset($data['subaccount'], $data['buyer'])) {
            return null;
        }

        return \json_decode(\json_encode($data));
    }
}
