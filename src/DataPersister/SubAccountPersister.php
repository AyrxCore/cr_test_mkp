<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Dto\Address;
use App\Dto\SubAccount;
use App\Dto\UserAccount;
use App\Entity\User;
use App\Service\UpplerAccountService;
use App\Service\UpplerBuyerCompanyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class SubAccountPersister implements ContextAwareDataPersisterInterface
{
    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UserPasswordHasherInterface $userPasswordHasher;

    #[Required]
    public NormalizerInterface $normalizer;

    #[Required]
    public UpplerAccountService $upplerAccountService;

    public function supports($data, array $context = []): bool
    {
        return $data instanceof SubAccount;
    }

    /**
     * @param SubAccount $data
     */
    public function persist($data, array $context = [])
    {
        $subAccount = new SubAccount();

        $subAccount->setId($data->getId());

        if (null !== $data->getBillingAddressId()) {
            $subAccount->setBillingAddressId($data->getBillingAddressId());
        }

        if (null !== $data->getShippingAddressId()) {
            $subAccount->setShippingAddressId($data->getShippingAddressId());
        }

        if (null !== $data->getEmail()) {
            $subAccount->setEmail($data->getEmail());
        }

        if (null !== $data->getLastName()) {
            $subAccount->setLastName($data->getLastName());
        }

        if (null !== $data->getFirstName()) {
            $subAccount->setFirstName($data->getFirstName());
        }

        if (null !== $data->getPhone()) {
            $subAccount->setPhone($data->getPhone());
        }

        try {
            return $this->upplerAccountService->updateUserSubAccountDatas($subAccount);
        } catch (\Exception $exception) {
            throw new \Exception('update account error: ' . $exception);
        }
    }

    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
    }
}
