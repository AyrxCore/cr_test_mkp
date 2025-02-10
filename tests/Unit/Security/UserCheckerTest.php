<?php

declare(strict_types=1);

use App\DataFixtures\Factory\AccountFactory;
use App\DataFixtures\Factory\UserFactory;
use App\Security\UserChecker;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

\it('throws an exception if api user is disabled', function () {
    $user = UserFactory::new([
        'enabled' => false,
        'roles' => ['ROLE_API'],
    ])
        ->withoutPersisting()
        ->create();

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user);
    $userChecker->checkPostAuth($user);
})
    ->throws(CustomUserMessageAccountStatusException::class, 'user_disabled');

\it('should not throw an exception if user is enabled and has ROLE_API role', function () {
    $this->expectNotToPerformAssertions();

    $user = UserFactory::new([
        'enabled' => true,
        'roles' => ['ROLE_API'],
    ])
        ->withoutPersisting()
        ->create();

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user);
    $userChecker->checkPostAuth($user);
});

\it('throws an exception if user has no account', function () {
    $user = UserFactory::new([
        'enabled' => true,
    ])
        ->withoutPersisting()
        ->create();

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user);
    $userChecker->checkPostAuth($user);
})
    ->throws(CustomUserMessageAccountStatusException::class, 'user_empty_account');

\it('throws an exception if user has no enabled account', function () {
    $account = AccountFactory::new(['enabled' => false])
        ->withoutPersisting()
        ->create();

    $user = UserFactory::new([
        'enabled' => true,
    ])
        ->withoutPersisting()
        ->create();
    $user->addAccount($account);

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user);
    $userChecker->checkPostAuth($user);
})
    ->throws(CustomUserMessageAccountStatusException::class, 'user_empty_account');

\it('throws an exception if user with account is disabled', function () {
    $account = AccountFactory::new(['enabled' => true])
        ->withoutPersisting()
        ->create();

    $user = UserFactory::new([
        'enabled' => false,
    ])
        ->withoutPersisting()
        ->create();
    $user->addAccount($account);

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user);
    $userChecker->checkPostAuth($user);
})
    ->throws(CustomUserMessageAccountStatusException::class, 'user_disabled');

\it('should not throw an exception if user is enabled and has at least one enabled accounts', function () {
    $this->expectNotToPerformAssertions();

    $account = AccountFactory::new(['enabled' => true])
        ->withoutPersisting()
        ->create();

    $account2 = AccountFactory::new(['enabled' => false])
        ->withoutPersisting()
        ->create();

    $user = UserFactory::new([
        'enabled' => true,
        'roles' => [],
    ])
        ->withoutPersisting()
        ->create();
    $user->addAccount($account);
    $user->addAccount($account2);

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user);
    $userChecker->checkPostAuth($user);
});
