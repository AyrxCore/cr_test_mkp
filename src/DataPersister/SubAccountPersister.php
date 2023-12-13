<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Dto\SubAccount;
use App\Entity\Account;
use App\Entity\Channel;
use App\Entity\UserInfoUpdateRequest;
use App\Events\ChangingEmailEvent;
use App\Events\UserInfoUpdateEvent;
use App\Repository\ChannelRepository;
use App\Service\UpplerAccountService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SubAccountPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private UpplerAccountService $upplerAccountService,
        private EventDispatcherInterface $eventDispatcher,
        private RequestStack $requestStack,
        private ChannelRepository $channelRepository,
    ) {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof SubAccount;
    }

    /**
     * @param SubAccount $data
     *
     * @throws \Exception
     */
    public function persist($data, array $context = [])
    {
        /** @var Account $account */
        $account = $this->em->getRepository(Account::class)->find($data->getAccountId());
        $user = $account->getUser();

        if ($data->getEmail() !== null) {
            $this->em->persist($user);

            $log = $this->em->getRepository(UserInfoUpdateRequest::class)->findOneBy([
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

            return true;
        }
        if (
            $data->getLastName() !== null
            || $data->getFirstName() !== null
            || $data->getPhone() !== null
        ) {
            if ($data->getLastName() !== null && $user->getLastName() !== $data->getLastName()) {
                $log = $this->em->getRepository(UserInfoUpdateRequest::class)->findOneBy([
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
                $log = $this->em->getRepository(UserInfoUpdateRequest::class)->findOneBy([
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
                $log = $this->em->getRepository(UserInfoUpdateRequest::class)->findOneBy([
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
                return $this->upplerAccountService->updateUserSubAccountData($subAccount);
            } catch (\Exception $exception) {
                throw new \Exception('update account error: '.$exception);
            }
        }

        return true;
    }

    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
    }
}
