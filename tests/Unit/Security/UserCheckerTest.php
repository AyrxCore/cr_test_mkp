<?php

declare(strict_types=1);

use App\DataFixtures\Factory\AccountFactory;
use App\DataFixtures\Factory\UserFactory;
use App\Security\UserChecker;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

\it('throws an exception if api user is disabled', function () {
    $user = UserFactory::new([
        'isEnabled' => false,
        'roles' => ['ROLE_API'],
    ])
        ->withoutPersisting()
        ->create();

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user->object());
    $userChecker->checkPostAuth($user->object());
})
    ->throws(CustomUserMessageAccountStatusException::class, 'user_disabled');

\it('should not throw an exception if user is enabled and has ROLE_API role', function () {
    $this->expectNotToPerformAssertions();

    $user = UserFactory::new([
        'isEnabled' => true,
        'roles' => ['ROLE_API'],
    ])
        ->withoutPersisting()
        ->create();

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user->object());
    $userChecker->checkPostAuth($user->object());
});

\it('throws an exception if user has no account', function () {
    $user = UserFactory::new([
        'isEnabled' => true,
    ])
        ->withoutPersisting()
        ->create();

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user->object());
    $userChecker->checkPostAuth($user->object());
})
    ->throws(CustomUserMessageAccountStatusException::class, 'user_empty_account');

\it('throws an exception if user has no enabled account', function () {
    $account = AccountFactory::new(['isEnabled' => false])
        ->withoutPersisting()
        ->create();

    $user = UserFactory::new([
        'isEnabled' => true,
    ])
        ->withoutPersisting()
        ->create();
    $user->addAccount($account->object());

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user->object());
    $userChecker->checkPostAuth($user->object());
})
    ->throws(CustomUserMessageAccountStatusException::class, 'user_empty_account');

\it('throws an exception if user with account is disabled', function () {
    $account = AccountFactory::new(['isEnabled' => true])
        ->withoutPersisting()
        ->create();

    $user = UserFactory::new([
        'isEnabled' => false,
    ])
        ->withoutPersisting()
        ->create();
    $user->addAccount($account->object());

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user->object());
    $userChecker->checkPostAuth($user->object());
})
    ->throws(CustomUserMessageAccountStatusException::class, 'user_disabled');

\it('should not throw an exception if user is enabled and has at least one enabled accounts', function () {
    $this->expectNotToPerformAssertions();

    $account = AccountFactory::new(['isEnabled' => true])
        ->withoutPersisting()
        ->create();

    $account2 = AccountFactory::new(['isEnabled' => true])
        ->withoutPersisting()
        ->create();

    $user = UserFactory::new([
        'isEnabled' => true,
        'roles' => [],
    ])
        ->withoutPersisting()
        ->create();
    $user->addAccount($account->object());
    $user->addAccount($account2->object());

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user->object());
    $userChecker->checkPostAuth($user->object());
});
