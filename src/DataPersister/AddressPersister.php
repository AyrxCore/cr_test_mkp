<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Dto\Address;
use App\Service\UpplerBuyerCompanyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class AddressPersister implements ContextAwareDataPersisterInterface
{
    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UserPasswordHasherInterface $userPasswordHasher;

    #[Required]
    public NormalizerInterface $normalizer;

    #[Required]
    public UpplerBuyerCompanyService $upplerBuyerCompanyService;

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Address;
    }

    /**
     * @param Address $data
     */
    public function persist($data, array $context = [])
    {
        $address = new Address();

        if (isset($context["item_operation_name"]) && 'update' === $context["item_operation_name"]) {
            $address->setId($data->getId());
        }

        $address->setType($data->getType());
        $address->setLastName($data->getLastName());
        $address->setFirstName($data->getFirstName());
        $address->setCity($data->getCity());
        $address->setCompany($data->getCompany());
        $address->setCompanyId($data->getCompanyId());
        $address->setCountry($data->getCountry());
        $address->setName($data->getName());
        $address->setPhone($data->getPhone());
        $address->setPostCode($data->getPostCode());
        $address->setStreet($data->getStreet());
        $result = [];

        if (isset($context["item_operation_name"]) && 'update' === $context["item_operation_name"]) {
            $result = $this->upplerBuyerCompanyService->updateAddress($address);
        } elseif (isset($context["collection_operation_name"]) && 'create' === $context["collection_operation_name"]) {
            $result = $this->upplerBuyerCompanyService->createAddress($address);
        }

        return $result;
    }

    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
    }
}
