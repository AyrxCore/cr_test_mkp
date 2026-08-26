<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\UserAccount;
use App\Entity\Account;
use App\Entity\Adherent;
use App\Entity\User;
use App\Repository\AccountRepository;
use App\Repository\AdherentRepository;
use App\Repository\ChannelRepository;
use App\Repository\UserInfoUpdateRequestRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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

        if ($user->getId() === null) {
            $existingByUsername = $this->userRepository->findOneBy(['username' => $data->getEmail()]);
            if ($existingByUsername !== null) {
                $user = $existingByUsername;
            }
        }

        $this->em->persist($user);

        $this->applyAccountData($account, $user, $adh, $data);

        if (!$logPhone || ($logPhone->getValue() === $data->getPhone())) {
            $account->setPhone($data->getPhone());
            if ($logPhone) {
                $logPhone->setIsIso(true);
                $logPhone->setIsoAt(new \DateTimeImmutable('now'));
                $this->em->persist($logPhone);
            }
        }

        $this->em->persist($account);
        $accountId = $account->getId();

        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException $e) {
            // Race condition : deux appels bot simultanés avec le même username.
            $this->em->clear();
            $existingUser = $this->userRepository->findOneBy(['username' => $data->getEmail()]);
            if ($existingUser === null) {
                throw new \RuntimeException(
                    \sprintf('UniqueConstraintViolation on email "%s" but no existing user found — unexpected constraint violation.', $data->getEmail()),
                    0,
                    $e,
                );
            }
            $user = $existingUser;
            $adhEntity = $this->adherentRepository->find($data->getAdherentId());
            if ($adhEntity === null) {
                // New Adherent rolled back with the failed flush; cannot safely retry without it
                throw new \RuntimeException(
                    \sprintf('Race condition recovery failed: Adherent "%s" not found after em->clear().', $data->getAdherentId()),
                    0,
                    $e,
                );
            }
            $account = $accountId !== null
                ? $this->accountRepository->find($accountId)
                : ($this->accountRepository->findOneByContactId($data->getContactId()) ?? new Account());
            $this->applyAccountData($account, $user, $adhEntity, $data);
            $this->em->persist($account);
            $this->em->flush();
        }

        $data->setAccountId($account->getId());
        $data->setUserId($user->getId());

        return $data;
    }

    private function applyAccountData(Account $account, User $user, Adherent $adh, UserAccount $data): void
    {
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
            ->setDjustPassword($data->getDjustPassword());
    }
}
