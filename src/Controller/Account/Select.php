<?php

declare(strict_types=1);

namespace App\Controller\Account;

use App\Context\ChannelContext;
use App\Entity\Account;
use App\Entity\User;
use App\Events\UserAcceptCGUEvent;
use App\Service\UpplerAuthenticationService;
use Psr\EventDispatcher\EventDispatcherInterface;
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
        private UpplerAuthenticationService $upplerAuthenticationService,
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

        $isAuthenticated = $this->upplerAuthenticationService->authenticateUser($account);

        if ($isAuthenticated && !$this->requestStack->getSession()->get('access_token')) {
            throw new AccessDeniedHttpException("Vous n'avez pas accès à ce compte");
        }

        if (empty($account->isAcceptCGU())) {
            $this->eventDispatcher->dispatch(new UserAcceptCGUEvent($account));
        }

        return new JsonResponse(['status' => 'ok']);
    }
}
