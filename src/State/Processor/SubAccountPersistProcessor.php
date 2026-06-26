<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\SubAccount;
use App\Entity\Account;
use App\Entity\User;
use App\Entity\UserInfoUpdateRequest;
use App\Events\ChangingEmailEvent;
use App\Events\UserInfoUpdateEvent;
use App\Repository\AccountRepository;
use App\Repository\ChannelRepository;
use App\Repository\UserInfoUpdateRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class SubAccountPersistProcessor implements ProcessorInterface
{
    public function __construct(
        private AccountRepository $accountRepository,
        private ChannelRepository $channelRepository,
        private EntityManagerInterface $em,
        private EventDispatcherInterface $eventDispatcher,
        private RequestStack $requestStack,
        private UserInfoUpdateRequestRepository $userInfoUpdateRequestRepository,
    ) {
    }

    /**
     * @throws \Random\RandomException
     * @throws \Exception
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): Account|SubAccount
    {
        /** @var Account $account */
        $account = $this->accountRepository->find($data->getAccountId());
        $user = $account->getUser();

        if ($data->getEmail() !== null) {
            $this->em->persist($user);

            $token = \md5(\random_bytes(100));
            $log = $this->updateUserAttribute(user: $user, attribute: 'email', newValue: $data->getEmail());
            $log->setEmailChangingRequestedAt(new \DateTime('now'));
            $log->setEmailChangingToken($token);

            $this->em->persist($log);
            $this->em->flush();

            $host = $this->requestStack->getMainRequest()->headers->get('host');
            $channel = $this->channelRepository->findOneBy([
                'hostname' => \preg_replace('/(.*):\d+/', '$1', $host),
            ]);

            $this->eventDispatcher->dispatch(new ChangingEmailEvent($user, $channel));
        }

        if (
            $data->getLastName() !== null
            || $data->getFirstName() !== null
            || $data->getPhone() !== null
        ) {
            if ($data->getLastName() !== null && $user->getLastName() !== $data->getLastName()) {
                $this->em->persist($this->updateUserAttribute(user: $user, attribute: 'lastname', newValue: $data->getLastName()));
                $user->setLastName($data->getLastName());
            }

            if ($data->getFirstName() !== null && $user->getFirstName() !== $data->getFirstName()) {
                $this->em->persist($this->updateUserAttribute(user: $user, attribute: 'firstname', newValue: $data->getFirstName()));
                $user->setFirstName($data->getFirstName());
            }

            if (!empty($data->getPhone())) {
                $formattedPhone = $this->formatPhoneNumber($data->getPhone());

                if ($account->getPhone() !== $formattedPhone) {
                    $log = $this->updateUserAttribute(user: $user, attribute: 'phone', newValue: $formattedPhone, account: $account);
                    $this->em->persist($log);
                    $account->setPhone($formattedPhone);
                }
            }

            $this->em->persist($user);
            $this->em->flush();

            $this->eventDispatcher->dispatch(new UserInfoUpdateEvent($user));
        }

        // TODO: Migration Djust - La mise à jour des adresses par défaut est désactivée pour le go live
        // Cette fonctionnalité sera implémentée localement après le go live
        // if (
        //     $data->getBillingAddressId() !== null
        //     || $data->getShippingAddressId() !== null
        // ) {
        //     $subAccount = new SubAccount();
        //     $subAccount->setId($data->getId());
        //     $subAccount->setBillingAddressId($data->getBillingAddressId());
        //     $subAccount->setShippingAddressId($data->getShippingAddressId());
        //
        //     try {
        //         $this->upplerAccountService->updateUserSubAccountData($subAccount);
        //     } catch (\Exception $exception) {
        //         throw new \Exception('update account error: '.$exception);
        //     }
        // }

        $response = new SubAccount();
        $response->setId($data->getId());
        $response->setLastName($user->getLastName());
        $response->setFirstName($user->getFirstName());
        $response->setPhone($account->getPhone());
        $response->setEmail($user->getEmail());
        $response->setBillingAddressId($data->getBillingAddressId());
        $response->setShippingAddressId($data->getShippingAddressId());

        return $response;
    }

    private function updateUserAttribute(
        User $user,
        string $attribute,
        string $newValue,
        ?Account $account = null,
    ): UserInfoUpdateRequest {
        $criteria = ['_user' => $user, 'attribute' => $attribute, 'isIso' => false];

        if ($account !== null) {
            $criteria['account'] = $account;
        }

        $log = $this->userInfoUpdateRequestRepository->findOneBy($criteria);

        if (!$log) {
            $log = new UserInfoUpdateRequest();
            $log->setUser($user);
            $log->setAttribute($attribute);
            $log->setIsIso(false);
            $oldValue = match ($attribute) {
                'email' => $user->getEmail(),
                'lastname' => $user->getLastName(),
                'firstname' => $user->getFirstName(),
                'phone' => $account?->getPhone() ?? '',
                default => '',
            };
            $log->setOldValue($oldValue);

            if ($account !== null) {
                $log->setAccount($account);
            }
        }

        $log->setValue($newValue);
        $log->setCreatedAt(new \DateTimeImmutable('now'));

        return $log;
    }

    private function formatPhoneNumber(string $phone): string
    {
        $cleaned = \str_replace(' ', '', $phone);

        if (\preg_match('/^0(\d{9})$/', $cleaned, $matches)) {
            $firstDigit = '0'.$matches[1][0];
            $remaining = \substr($matches[1], 1);

            return $firstDigit.' '.\implode(' ', \str_split($remaining, 2));
        } elseif (\preg_match('/^\+33(\d{9})$/', $cleaned, $matches)) {
            $firstDigit = $matches[1][0];
            $remaining = \substr($matches[1], 1);

            return '+33 '.$firstDigit.' '.\implode(' ', \str_split($remaining, 2));
        } elseif (\str_contains($cleaned, '+')) {
            return $phone; // numéro étranger, on ne touche pas
        }

        return $cleaned;
    }
}
