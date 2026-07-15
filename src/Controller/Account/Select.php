<?php

declare(strict_types=1);

namespace App\Controller\Account;

use App\Context\ChannelContext;
use App\Entity\Account;
use App\Entity\User;
use App\Events\UserAcceptCGUEvent;
use App\Service\Account\CurrentAccountProvider;
use App\Service\Djust\DjustAuthenticationService;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class Select extends AbstractController
{
    public function __construct(
        private ChannelContext $channelContext,
        private EventDispatcherInterface $eventDispatcher,
        private RequestStack $requestStack,
        private Security $security,
        private readonly DjustAuthenticationService $djustAuthenticationService,
        protected readonly LoggerInterface $djustLogger,
    ) {
    }

    #[Route(
        path: '/api/accounts/{id}/select',
        name: 'account_select',
        defaults: [
            '_api_resource_class' => Account::class,
        ],
        methods: ['GET']
    )]
    public function __invoke(Account $account)
    {
        try {
            /** @var User $currentUser */
            $currentUser = $this->security->getUser();

            if (!$currentUser->getAccounts()->contains($account)) {
                throw new AccessDeniedHttpException('This account does not belong to the current user');
            }

            if (!$account->isEnabled()) {
                throw new AccessDeniedHttpException('Account is disabled');
            }

            $channel = $this->channelContext->getChannel();

            if ($account->getAdherent()?->getChannel()?->getCode() !== $channel->getCode()) {
                throw new AccessDeniedHttpException('Account is not linked to current channel');
            }
            $session = $this->requestStack->getSession();
            $session->set(CurrentAccountProvider::SESSION_KEY_ACCOUNT, $account);

            $isDjustAuthenticated = $this->djustAuthenticationService->authenticateUser($account);
            if (!$isDjustAuthenticated) {
                throw new AccessDeniedHttpException();
            }

            if (empty($account->isAcceptCGU())) {
                $this->eventDispatcher->dispatch(new UserAcceptCGUEvent($account));
            }

            return new JsonResponse(['status' => 'ok']);
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }
}
