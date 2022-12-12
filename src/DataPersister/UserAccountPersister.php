<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Dto\UserAccount;
use App\Entity\Account;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class UserAccountPersister implements ContextAwareDataPersisterInterface
{
    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UserPasswordHasherInterface $userPasswordHasher;

    #[Required]
    public NormalizerInterface $normalizer;

    public function supports($data, array $context = []): bool
    {
        return $data instanceof UserAccount;
    }

    /**
     * @param UserAccount $data
     */
    public function persist($data, array $context = [])
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $data->getEmail()]);
        if (!$user) {
            $user = new User();
            $user->setUsername($data->getEmail());
            $user->setEmail($data->getEmail());
            $user->setPassword($this->userPasswordHasher->hashPassword($user, uniqid()));
            $user->setEnabled(false);
            $this->em->persist($user);
        }

        $account = $this->em->getRepository(Account::class)->findOneBy(
            ['upplerUsername' => $data->getUpplerSubAccountUsername()]
        );

        if ($account) {
           throw  new BadRequestException("Account with this username already exist");
        }

        $account = new Account();
        $account->setUpplerSubAccountId($data->getUpplerSubAccountId());
        $account->setUpplerUsername($data->getUpplerSubAccountUsername());
        $account->setUpplerPassword($data->getUpplerSubAccountPassword());
        $account->setUpplerUserId($data->getUpplerUserId());
        $account->setUpplerCompanyId($data->getUpplerCompanyId());
        $account->setUser($user);
        $account->setIsEnabled(false);
        $this->em->persist($account);
        $this->em->flush();

        $data->setAccountId($account->getId());
        $data->setUserId($user->getId());

        return $data;
    }

    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
    }
}
