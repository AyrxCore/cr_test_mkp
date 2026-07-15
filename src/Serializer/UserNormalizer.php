<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\User;
use App\Repository\AccountRepository;
use App\Service\Account\CurrentAccountProvider;
use App\Service\Djust\DjustCustomerAccountService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface as SfNormalizerInterface;

class UserNormalizer extends AbstractNormalizer
{
    public function __construct(
        SfNormalizerInterface $normalizer,
        private readonly RequestStack $requestStack,
        private readonly AccountRepository $accountRepository,
        private readonly DjustCustomerAccountService $djustCustomerAccountService,
    ) {
        $this->normalizer = $normalizer;
    }

    /**
     * @param User $object
     *
     * @throws ExceptionInterface
     */
    public function normalize($object, $format = null, array $context = []): array|null
    {
        $serializationGroups = $this->getSerializationGroups($context);

        if (\in_array('user:me', $serializationGroups, true)) {
            try {
                $session = $this->requestStack->getSession();

                // Récupération de l'ID account depuis la session
                $accountData = $session->get(CurrentAccountProvider::SESSION_KEY_ACCOUNT);
                $accountId = $accountData->getId();

                if ($accountId) {
                    // Récupération de l'entité fraîche depuis la BDD
                    $account = $this->accountRepository->find($accountId);
                    if ($account) {
                        $object->setCurrentAccount($account);
                    }
                }
            } catch (\Exception $e) {
                // Session non disponible, on supprime la session pour rediriger vers la homepage
                $session->remove(CurrentAccountProvider::SESSION_KEY_ACCOUNT);
            }
        }

        return parent::normalize($object, $format, $context);
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof User && $this->normalizer->supportsNormalization($data, $format);
    }

    /**
     * @param User $object
     */
    protected function getExternalApiData(mixed $object, array $context): ?\stdClass
    {
        $serializationGroups = $this->getSerializationGroups($context);

        if (!\in_array('user:external_api_data', $serializationGroups, true)) {
            return null;
        }

        $data = [];

        if (\in_array('user:external_api_data', $serializationGroups, true)) {
            $customerAccount = $this->djustCustomerAccountService->getCustomerAccount();
            if ($customerAccount !== null) {
                $data['customerAccount'] = $customerAccount;
            }
        }

        $data = \array_filter($data, fn ($value) => $value !== null);

        return empty($data) ? null : \json_decode(\json_encode($data));
    }
}
