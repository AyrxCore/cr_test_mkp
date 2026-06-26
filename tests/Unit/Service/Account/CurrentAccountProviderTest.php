<?php

declare(strict_types=1);

use App\Entity\Account;
use App\Service\Account\CurrentAccountProvider;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

\uses()->group('UnitCurrentAccountProvider');

\beforeEach(function () {
    $this->session = Mockery::mock(SessionInterface::class);
    $this->requestStack = Mockery::mock(RequestStack::class);
    $this->requestStack->shouldReceive('getSession')->andReturn($this->session);

    $this->provider = new CurrentAccountProvider($this->requestStack);
});

\afterEach(function () {
    Mockery::close();
});

\it('returns account when session contains a valid Account', function () {
    $account = Mockery::mock(Account::class);

    $this->session->shouldReceive('get')
        ->with(CurrentAccountProvider::SESSION_KEY_ACCOUNT)
        ->andReturn($account);

    $result = $this->provider->getRequiredAccount();

    \expect($result)->toBe($account);
});

\it('throws AuthenticationException when session contains no account', function () {
    $this->session->shouldReceive('get')
        ->with(CurrentAccountProvider::SESSION_KEY_ACCOUNT)
        ->andReturn(null);

    $this->provider->getRequiredAccount();
})->throws(AuthenticationException::class);

\it('throws AuthenticationException when session contains a non-Account value', function () {
    $this->session->shouldReceive('get')
        ->with(CurrentAccountProvider::SESSION_KEY_ACCOUNT)
        ->andReturn('not-an-account');

    $this->provider->getRequiredAccount();
})->throws(AuthenticationException::class);
