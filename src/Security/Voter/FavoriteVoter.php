<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Account;
use App\Entity\Favorite;
use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Contracts\Service\Attribute\Required;

class FavoriteVoter extends Voter
{
    public const string VIEW_FAVORITE = 'VIEW_FAVORITE';
    public const string EDIT_FAVORITE = 'EDIT_FAVORITE';
    public const string DELETE_FAVORITE = 'DELETE_FAVORITE';
    #[Required]
    public RequestStack $requestStack;

    protected function supports(string $attribute, $subject): bool
    {
        if (!\in_array($attribute, [self::VIEW_FAVORITE, self::EDIT_FAVORITE, self::DELETE_FAVORITE], true)) {
            return false;
        }

        $favorite = $subject;

        return $favorite instanceof Favorite;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        /** @var Favorite $favorite */
        $favorite = $subject;

        /** @var Account $account */
        $account = $this->requestStack->getSession()->get('account');

        return match ($attribute) {
            self::VIEW_FAVORITE => $this->canView($favorite, $user, $account),
            self::EDIT_FAVORITE => $this->canEdit($favorite, $account),
            self::DELETE_FAVORITE => $this->canDelete($favorite, $account),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canView(Favorite $favorite, User $user, Account $account): bool
    {
        return $favorite->getAccount()->getId()->equals($account->getId())
            || ($favorite->isPublic() && $favorite->getAccount()->getAdherent()->getId()->equals($account->getAdherent()->getId()));
    }

    private function canEdit(Favorite $favorite, Account $account): bool
    {
        return $favorite->getAccount()->getId()->equals($account->getId());
    }

    private function canDelete(Favorite $favorite, Account $account): bool
    {
        // Si on peut modifier, on peut supprimer
        return $this->canEdit($favorite, $account);
    }
}
