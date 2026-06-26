<?php

declare(strict_types=1);

namespace App\Service\Account;

use App\Entity\Account;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class CurrentAccountProvider
{
    public const string SESSION_KEY_ACCOUNT = 'account';

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function getAccount(): ?Account
    {
        $account = $this->requestStack->getSession()->get(self::SESSION_KEY_ACCOUNT);

        return $account instanceof Account ? $account : null;
    }

    public function getRequiredAccount(): Account
    {
        return $this->getAccount() ?? throw new AuthenticationException('Aucun compte sélectionné.');
    }
}
