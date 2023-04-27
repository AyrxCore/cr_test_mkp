<?php

namespace App\Security\Voter;

use App\Entity\Favorite;
use App\Entity\User;
use App\Entity\Account;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class FavoriteVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject)
    {
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        [$favorite, $account] = $subject;
        if (!$favorite instanceof Favorite || !$account instanceof Account) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool|int
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            // L'utilisateur doit être loggué avant de pouvoir accéder
            return false;
        }

        /** @var Favorite $favorite
         *  @var Account $account
         */
        [$favorite, $account] = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($favorite, $user, $account),
            self::EDIT => $this->canEdit($favorite, $account),
            self::DELETE => $this->canDelete($favorite, $account),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canView(Favorite $favorite, User $user, Account $account): bool
    {
        if (
            $favorite->getAccount()->getId()->equals($account->getId()) ||
            (in_array($favorite->getAccount(), $user->getAccounts()->toArray()) && $favorite->isPublic())
        ) {
            return true;
        }

        return false;
    }

    private function canEdit(Favorite $favorite, Account $account): bool
    {
        if ($favorite->getAccount()->getId()->equals($account->getId())) {
            return true;
        }

        return false;
    }

    private function canDelete(Favorite $favorite, Account $account): bool
    {
        // Si on peut modifier, on peut supprimer
        if ($this->canEdit($favorite, $account)) {
            return true;
        }

        return false;
    }
}
