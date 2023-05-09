<?php

namespace App\Security\Voter;

use App\Entity\SavedCart;
use App\Entity\User;
use App\Entity\Account;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SavedCartVoter extends Voter
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

        [$savedCart, $account] = $subject;
        if (!$savedCart instanceof SavedCart || !$account instanceof Account) {
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

        /** @var SavedCart $savedCart
         *  @var Account $account
         */
        [$savedCart, $account] = $subject;

        return match($attribute) {
            self::VIEW, self::EDIT, self::DELETE => $this->canAccess($savedCart, $account),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canAccess(SavedCart $savedCart, Account $account): bool
    {
        return $savedCart->getAccount()->getId()->equals($account->getId());
    }
}
