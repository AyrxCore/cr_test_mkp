<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Account;
use App\Entity\Adherent;
use App\Entity\User;
use App\Repository\AccountRepository;
use App\Repository\AdherentRepository;
use App\Repository\ChannelRepository;
use App\Repository\UserInfoUpdateRequestRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

readonly class UserAccountPersistProcessor implements ProcessorInterface
{
    public function __construct(
        private AccountRepository $accountRepository,
        private AdherentRepository $adherentRepository,
        private ChannelRepository $channelRepository,
        private EntityManagerInterface $em,
        private UserInfoUpdateRequestRepository $userInfoUpdateRequestRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
        private UserRepository $userRepository,
    ) {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $adh = $this->adherentRepository->find($data->getAdherentId());

        if ($adh === null) {
            $adh = new Adherent();
            $parent = $data->getAdherentParentId() ? $this->adherentRepository->find($data->getAdherentParentId()) : null;
            $adh->setId(new Uuid($data->getAdherentId()))
                ->setParent($parent)
                ->setName($data->getAdherentName())
                ->setChannel($this->channelRepository->findOneByCode($data->getChannelCode()));
            $this->em->persist($adh);
        }

        if ($data->getAccountId()) {
            $account = $this->accountRepository->findOneBy(
                ['id' => $data->getAccountId()]
            );
            $user = $account->getUser();
        } else {
            $user = $this->userRepository->findOneBy(['username' => $data->getEmail()]);
            if (!$user) {
                $user = new User();
                $user->setEnabled(false);
                $user->setPassword($this->userPasswordHasher->hashPassword($user, \uniqid()));
            }
            $account = $this->accountRepository->findOneByContactId($data->getContactId());
            if (!$account) {
                $account = new Account();
            }
        }

        $logEmail = $this->userInfoUpdateRequestRepository->findOneBy([
            '_user' => $user,
            'attribute' => 'email',
            'isIso' => false,
        ]);
        $logLastname = $this->userInfoUpdateRequestRepository->findOneBy([
            '_user' => $user,
            'attribute' => 'lastname',
            'isIso' => false,
        ]);
        $logFirstname = $this->userInfoUpdateRequestRepository->findOneBy([
            '_user' => $user,
            'attribute' => 'firstname',
            'isIso' => false,
        ]);
        $logPhone = $this->userInfoUpdateRequestRepository->findOneBy([
            'account' => $account,
            'attribute' => 'phone',
            'isIso' => false,
        ]);

        if (!$logEmail || ($logEmail->getValue() === $data->getEmail())) {
            $sameMailUser = $this->userRepository->findOneBy(['email' => $data->getEmail()]);
            if (\count($user->getAccounts()) > 1 && $data->getEmail() !== $user->getEmail() && !$sameMailUser) {
                $newUser = new User();
                $newUser->setEnabled($user->isEnabled());
                $newUser->setPassword($user->getPassword());
                $user = $newUser;
                if ($logEmail) {
                    $logEmail->setUser($user);
                }
            } else {
                if ($sameMailUser) {
                    $user = $sameMailUser;
                }
            }

            $user->setUsername($data->getEmail());
            $user->setEmail($data->getEmail());
            if ($logEmail) {
                $logEmail->setIsIso(true);
                $logEmail->setIsoAt(new \DateTimeImmutable('now'));
                $this->em->persist($logEmail);
            }
        }

        if (!$logFirstname || ($logFirstname->getValue() === $data->getFirstname())) {
            $user->setFirstName($data->getFirstname());
            if ($logFirstname) {
                $logFirstname->setIsIso(true);
                $logFirstname->setIsoAt(new \DateTimeImmutable('now'));
                $this->em->persist($logFirstname);
            }
        }

        if (!$logLastname || ($logLastname->getValue() === $data->getLastname())) {
            $user->setLastName($data->getLastname());
            if ($logLastname) {
                $logLastname->setIsIso(true);
                $logLastname->setIsoAt(new \DateTimeImmutable('now'));
                $this->em->persist($logLastname);
            }
        }

        $this->em->persist($user);

        $account
            ->setServiceFonction($data->getServiceFonction())
            ->setPhone($data->getPhone())
            ->setUser($user)
            ->setEnabled($data->isEnabled())
            ->setAdherent($adh)
            ->setContactId(Uuid::fromString($data->getContactId()))
            ->setDjustCustomerAccountId($data->getDjustCustomerAccountId())
            ->setDjustCustomerUserId($data->getDjustCustomerUserId())
            ->setDjustUsername($data->getDjustUsername())
            ->setDjustPassword($data->getDjustPassword())
            ->setUpplerSubAccountId($data->getUpplerSubAccountId())
            ->setUpplerClientId($data->getUpplerSubAccountClientId())
            ->setUpplerClientSecret($data->getUpplerSubAccountClientSecret())
            ->setUpplerUserId($data->getUpplerUserId())
            ->setUpplerCompanyId($data->getUpplerCompanyId());

        if (!$logPhone || ($logPhone->getValue() === $data->getPhone())) {
            $account->setPhone($data->getPhone());
            if ($logPhone) {
                $logPhone->setIsIso(true);
                $logPhone->setIsoAt(new \DateTimeImmutable('now'));
                $this->em->persist($logPhone);
            }
        }

        $this->em->persist($account);
        $this->em->flush();

        $data->setAccountId($account->getId());
        $data->setUserId($user->getId());

        return $data;
    }
}
