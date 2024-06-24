<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Account;
use App\Entity\SavedCart;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SavedCartVoter extends Voter
{
    private const string MANAGE_SAVED_CART = 'MANAGE_SAVED_CART';

    public function __construct(private RequestStack $requestStack)
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return $subject instanceof SavedCart && $attribute === self::MANAGE_SAVED_CART;
    }

    /**
     * @param SavedCart $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var Account $account */
        $account = $this->requestStack->getSession()->get('account');

        return $subject->getAccount()->getId()->equals($account->getId());
    }
}
