<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\SubAccount;
use App\Entity\Account;
use App\Entity\UserInfoUpdateRequest;
use App\Events\ChangingEmailEvent;
use App\Events\UserInfoUpdateEvent;
use App\Repository\AccountRepository;
use App\Repository\ChannelRepository;
use App\Repository\UserInfoUpdateRequestRepository;
use App\Service\UpplerAccountService;
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
        private UpplerAccountService $upplerAccountService,
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

            $log = $this->userInfoUpdateRequestRepository->findOneBy([
                '_user' => $user,
                'attribute' => 'email',
                'isIso' => 'false',
            ]);
            if (!$log) {
                $log = new UserInfoUpdateRequest();
                $log->setUser($user);
                $log->setAttribute('email');
                $log->setIsIso(false);
                $log->setOldValue($user->getEmail());
            }
            $log->setValue($data->getEmail());
            $log->setCreatedAt(new \DateTimeImmutable('now'));

            $log->setEmailChangingRequestedAt(new \DateTime('now'));
            $token = \md5(\random_bytes(100));
            $log->setEmailChangingToken($token);

            $this->em->persist($log);

            $this->em->flush();

            $host = $this->requestStack->getMainRequest()->headers->get('host');

            $channel = $this->channelRepository->findOneBy([
                'hostname' => \preg_replace('/(.*):\d+/', '$1', $host),
            ]);

            $this->eventDispatcher->dispatch(new ChangingEmailEvent($user, $channel));

            return $account;
        }
        if (
            $data->getLastName() !== null
            || $data->getFirstName() !== null
            || $data->getPhone() !== null
        ) {
            if ($data->getLastName() !== null && $user->getLastName() !== $data->getLastName()) {
                $log = $this->userInfoUpdateRequestRepository->findOneBy([
                    '_user' => $user,
                    'attribute' => 'lastname',
                    'isIso' => 'false',
                ]);
                if (!$log) {
                    $log = new UserInfoUpdateRequest();
                    $log->setUser($user);
                    $log->setAttribute('lastname');
                    $log->setIsIso(false);
                    $log->setOldValue($user->getLastName());
                }
                $log->setValue($data->getLastName());
                $log->setCreatedAt(new \DateTimeImmutable('now'));
                $this->em->persist($log);
                $user->setLastName($data->getLastName());
            }
            if ($data->getFirstName() !== null && $user->getFirstName() !== $data->getFirstName()) {
                $log = $this->userInfoUpdateRequestRepository->findOneBy([
                    '_user' => $user,
                    'attribute' => 'firstname',
                    'isIso' => 'false',
                ]);
                if (!$log) {
                    $log = new UserInfoUpdateRequest();
                    $log->setUser($user);
                    $log->setAttribute('firstname');
                    $log->setIsIso(false);
                    $log->setOldValue($user->getFirstName());
                }
                $log->setValue($data->getFirstName());
                $log->setCreatedAt(new \DateTimeImmutable('now'));
                $this->em->persist($log);
                $user->setFirstName($data->getFirstName());
            }

            if (!empty($data->getPhone()) && $account->getPhone() !== $data->getPhone()) {
                $log = $this->userInfoUpdateRequestRepository->findOneBy([
                    'account' => $account,
                    'attribute' => 'phone',
                    'isIso' => 'false',
                ]);

                if (!$log) {
                    $log = new UserInfoUpdateRequest();
                    $log->setUser($user);
                    $log->setAccount($account);
                    $log->setAttribute('phone');
                    $log->setIsIso(false);
                    $log->setOldValue($account->getPhone() ?? '');
                }
                $log->setValue($data->getPhone());
                $log->setCreatedAt(new \DateTimeImmutable('now'));
                $this->em->persist($log);
                $account->setPhone($data->getPhone());
            }

            $this->em->persist($user);
            $this->em->flush();

            $event = new UserInfoUpdateEvent($user);
            $this->eventDispatcher->dispatch($event);
        }

        if (
            $data->getBillingAddressId() !== null
            || $data->getShippingAddressId() !== null
        ) {
            $subAccount = new SubAccount();
            $subAccount->setId($data->getId());
            $subAccount->setBillingAddressId($data->getBillingAddressId());
            $subAccount->setShippingAddressId($data->getShippingAddressId());

            try {
                $this->upplerAccountService->updateUserSubAccountData($subAccount);
            } catch (\Exception $exception) {
                throw new \Exception('update account error: '.$exception);
            }
        }

        return $account;
    }
}
