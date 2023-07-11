<?php

namespace App\Security\Voter;

use App\Entity\Favorite;
use App\Entity\User;
use App\Entity\Account;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Contracts\Service\Attribute\Required;

class FavoriteVoter extends Voter
{
    #[Required]
    public RequestStack $requestStack;

    public const VIEW_FAVORITE = 'VIEW_FAVORITE';
    public const EDIT_FAVORITE = 'EDIT_FAVORITE';
    public const DELETE_FAVORITE = 'DELETE_FAVORITE';

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject)
    {
        if (!in_array($attribute, [self::VIEW_FAVORITE, self::EDIT_FAVORITE, self::DELETE_FAVORITE])) {
            return false;
        }

        $favorite = $subject;

        return $favorite instanceof Favorite;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool|int
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
        return $favorite->getAccount()->getId()->equals($account->getId()) ||
            ($favorite->isPublic() && $favorite->getAccount()->getAdherent()->getId()->equals($account->getAdherent()->getId()));
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
