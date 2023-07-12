<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Dto\SubAccount;
use App\Entity\Account;
use App\Entity\UserInfoUpdateRequest;
use App\Events\ChangingEmailEvent;
use App\Events\UserInfoUpdateEvent;
use App\Service\MailerProvider;
use App\Service\UpplerAccountService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Twig\Environment;


class SubAccountPersister implements ContextAwareDataPersisterInterface
{

    #[Required]
    public Environment $twig;

    #[Required]
    public MailerProvider $mailerProvider;

    #[Required]
    public ParameterBagInterface $parameterBag;

    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UserPasswordHasherInterface $userPasswordHasher;

    #[Required]
    public NormalizerInterface $normalizer;

    #[Required]
    public UpplerAccountService $upplerAccountService;

    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public EventDispatcherInterface $eventDispatcher;

    #[Required]
    public JWTTokenManagerInterface $JWTTokenManager;

    public function supports($data, array $context = []): bool
    {
        return $data instanceof SubAccount;
    }

    /**
     * @param  SubAccount  $data
     */
    public function persist($data, array $context = [])
    {
        /** @var Account $account */
        $account = $this->em->getRepository(Account::class)->find($data->getId());
        $user = $account->getUser();

        if (null !== $data->getEmail()) {
            $this->em->persist($user);

            $log = $this->em->getRepository(UserInfoUpdateRequest::class)->findOneBy([
                '_user'     => $user,
                'attribute' => 'email',
                'isIso'     => 'false',
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
            $token = md5(random_bytes(100));
            $log->setEmailChangingToken($token);

            $this->em->persist($log);

            $this->em->flush();

            $event = new ChangingEmailEvent($user);
            $this->eventDispatcher->dispatch($event);

            return true;
        }
        if (
            null !== $data->getLastName()
            || null !== $data->getFirstName()
            || null !== $data->getPhone()
        ) {
            if (null !== $data->getLastName() && $user->getLastName() !== $data->getLastName()) {
                $log = $this->em->getRepository(UserInfoUpdateRequest::class)->findOneBy([
                    '_user'     => $user,
                    'attribute' => 'lastname',
                    'isIso'     => 'false',
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
            if (null !== $data->getFirstName() && $user->getFirstName() !== $data->getFirstName()) {
                $log = $this->em->getRepository(UserInfoUpdateRequest::class)->findOneBy([
                    '_user'     => $user,
                    'attribute' => 'firstname',
                    'isIso'     => 'false',
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
            if (null !== $data->getPhone() && $account->getPhone() !== $data->getPhone()) {
                $log = $this->em->getRepository(UserInfoUpdateRequest::class)->findOneBy([
                    'account'   => $account,
                    'attribute' => 'phone',
                    'isIso'     => 'false',
                ]);
                if (!$log) {
                    $log = new UserInfoUpdateRequest();
                    $log->setAccount($account);
                    $log->setAttribute('phone');
                    $log->setIsIso(false);
                    $log->setOldValue($account->getPhone());
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
            null !== $data->getBillingAddressId()
            || null !== $data->getShippingAddressId()
        ) {
            $subAccount = new SubAccount();
            $subAccount->setId($data->getId());

            try {
                return $this->upplerAccountService->updateUserSubAccountDatas($subAccount);
            } catch (\Exception $exception) {
                throw new \Exception('update account error: ' . $exception);
            }
        }
        return true;
    }

    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
    }

}
