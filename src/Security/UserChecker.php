<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->isEnabled()) {
            throw new CustomUserMessageAccountStatusException('user_disabled');
        }

        if (!$user->isAccesMarketPlace()) {
            throw new CustomUserMessageAccountStatusException('user_disabled');
        }

        if (!$user->hasRole('ROLE_API')) {
            if ($user->getAccounts()->isEmpty()) {
                throw new CustomUserMessageAccountStatusException('user_empty_account');
            }

            if (!$this->hasEnabledAccount($user)) {
                throw new CustomUserMessageAccountStatusException('user_disabled');
            }
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        // Nothing
    }

    private function hasEnabledAccount(User $user): bool
    {
        foreach ($user->getAccounts() as $account) {
            if ($account->isEnabled()) {
                return true;
            }
        }

        return false;
    }
}
