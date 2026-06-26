<?php

declare(strict_types=1);

use App\Entity\Account;
use App\Entity\User;
use App\Security\UserChecker;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

\it('throws an exception if api user is disabled', function () {
    $user = \Mockery::mock(User::class);
    $user->shouldReceive('isEnabled')->andReturn(false);
    $user->shouldReceive('hasRole')->with('ROLE_API')->andReturn(true);

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user);
    $userChecker->checkPostAuth($user);
})
    ->throws(CustomUserMessageAccountStatusException::class, 'user_disabled')
    ->group('UnitUserChecker');

\it('should not throw an exception if user is enabled and has ROLE_API role', function () {
    $this->expectNotToPerformAssertions();

    $user = \Mockery::mock(User::class);
    $user->shouldReceive('isEnabled')->andReturn(true);
    $user->shouldReceive('hasRole')->with('ROLE_API')->andReturn(true);

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user);
    $userChecker->checkPostAuth($user);
})->group('UnitUserChecker');

\it('throws an exception if user has no account', function () {
    $user = \Mockery::mock(User::class);
    $user->shouldReceive('isEnabled')->andReturn(true);
    $user->shouldReceive('hasRole')->with('ROLE_API')->andReturn(false);
    $user->shouldReceive('getFirstEnabledAccount')->andReturn(null);

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user);
    $userChecker->checkPostAuth($user);
})
    ->throws(CustomUserMessageAccountStatusException::class, 'user_empty_account')
    ->group('UnitUserChecker');

\it('throws an exception if user has no enabled account', function () {
    $user = \Mockery::mock(User::class);
    $user->shouldReceive('isEnabled')->andReturn(true);
    $user->shouldReceive('hasRole')->with('ROLE_API')->andReturn(false);
    $user->shouldReceive('getFirstEnabledAccount')->andReturn(null);

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user);
    $userChecker->checkPostAuth($user);
})
    ->throws(CustomUserMessageAccountStatusException::class, 'user_empty_account')
    ->group('UnitUserChecker');

\it('throws an exception if user with account is disabled', function () {
    $account = \Mockery::mock(Account::class);
    $account->shouldReceive('isEnabled')->andReturn(true);

    $user = \Mockery::mock(User::class);
    $user->shouldReceive('isEnabled')->andReturn(false);

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user);
    $userChecker->checkPostAuth($user);
})->throws(CustomUserMessageAccountStatusException::class, 'user_disabled')
    ->group('UnitUserChecker');

\it('should not throw an exception if user is enabled and has at least one enabled accounts', function () {
    $this->expectNotToPerformAssertions();

    $account = \Mockery::mock(Account::class);
    $account->shouldReceive('isEnabled')->andReturn(true);

    $user = \Mockery::mock(User::class);
    $user->shouldReceive('isEnabled')->andReturn(true);
    $user->shouldReceive('hasRole')->with('ROLE_API')->andReturn(false);
    $user->shouldReceive('getFirstEnabledAccount')->andReturn($account);

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user);
    $userChecker->checkPostAuth($user);
})->group('UnitUserChecker');
