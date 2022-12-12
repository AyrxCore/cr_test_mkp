<?php
namespace App\Security;

use App\Entity\Account;
use App\Entity\User;
use App\Service\UpplerAuthenticationService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Service\Attribute\Required;

class UserChecker implements UserCheckerInterface
{
    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public UpplerAuthenticationService $upplerAuthenticationService;

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        //le compte n'est pas actif on refuse l'authentification,l'utilisateur doit passer par 'première connexion'
        if (!$user->isEnabled()) {
            throw new CustomUserMessageAccountStatusException('user_disabled');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        //le user n'est lié à aucun compte, il ne peut pas entrer saufs'il s'agit d'un utilisteur api (bot Neo)
        if (!$user->hasRole('ROLE_API') && $user->getAccounts()->isEmpty()) {
            throw new CustomUserMessageAccountStatusException('user_empty_account');
        } elseif (1 === $user->getAccounts()->count()) {
            //le user est lié à un seul compte on l'identifie automatiquement dessus
            $session = $this->requestStack->getSession();
            $session->start();

            /**@var Account $account*/
            $account = $user->getAccounts()->first();
            $userAuth = $this->upplerAuthenticationService->authenticateUser(
                $account
            );

            if ($userAuth && $session->has('access_token') && !empty($session->get('access_token'))) {
                return;
            }
        }
    }
}
