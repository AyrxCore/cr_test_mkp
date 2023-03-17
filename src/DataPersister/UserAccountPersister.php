<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Dto\AccountAccordCadre;
use App\Dto\UserAccount;
use App\Entity\AccordStatut;
use App\Entity\Account;
use App\Entity\Adherent;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Uid\Uuid;
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
     * @param  UserAccount  $data
     */
    public function persist($data, array $context = [])
    {
        $adh = $this->em->getRepository(Adherent::class)->find($data->getAdherentId());

        if (null === $adh) {
            $adh = new Adherent();
            $adh->setId(new Uuid($data->getAdherentId()));
            $adh->setName($data->getAdherentName());
        }
        $this->em->persist($adh);

        if ($data->getAccountId()) {
            /** @var \App\Entity\Account $account */
            $account = $this->em->getRepository(Account::class)->findOneBy(
                ['id' => $data->getAccountId()]
            );
            $user = $account->getUser();
        } else {
            $user = $this->em->getRepository(User::class)->findOneBy(['username' => $data->getEmail()]);
            if (!$user) {
                $user = new User();
                $user->setEnabled(false);
            }
            $account = $this->em->getRepository(Account::class)->findOneBy(
                ['upplerClientId' => $data->getUpplerSubAccountClientId()]
            );
            if ($account) {
                throw  new BadRequestException("Account with this username already exist");
            }
            $account = new Account();
        }

        $user->setAccesMarketPlace($data->isMarketplace());
        $user->setUsername($data->getEmail());
        $user->setFirstName($data->getFirstname());
        $user->setLastName($data->getLastname());
        $user->setEmail($data->getEmail());
        $user->setPassword($this->userPasswordHasher->hashPassword($user, uniqid()));
        $this->em->persist($user);


        $account->setUpplerSubAccountId($data->getUpplerSubAccountId());
        $account->setUpplerClientId($data->getUpplerSubAccountClientId());
        $account->setUpplerClientSecret($data->getUpplerSubAccountClientSecret());
        $account->setUpplerUserId($data->getUpplerUserId());
        $account->setUpplerCompanyId($data->getUpplerCompanyId());
        $account->setUser($user);
        $account->setIsEnabled(true);

        $account->setAdherent($adh);
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
