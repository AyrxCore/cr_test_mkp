<?php

declare(strict_types=1);

use App\DataFixtures\Factory\AccountFactory;
use App\DataFixtures\Factory\UserFactory;
use App\Security\UserChecker;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

\it('throws an exception if user is disabled', function () {
    $user = UserFactory::new([
        'isEnabled' => false,
    ])
        ->withoutPersisting()
        ->create();

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user->object());
    $userChecker->checkPostAuth($user->object());
})
    ->throws(CustomUserMessageAccountStatusException::class, 'user_disabled');

\it('throws an exception if user has no access to marketplace', function () {
    $user = UserFactory::new([
        'isEnabled' => true,
        'accesMarketPlace' => false,
    ])
        ->withoutPersisting()
        ->create();

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user->object());
    $userChecker->checkPostAuth($user->object());
})
    ->throws(CustomUserMessageAccountStatusException::class, 'user_disabled');

\it('throws an exception if user an no account', function () {
    $user = UserFactory::new([
        'isEnabled' => true,
        'accesMarketPlace' => true,
    ])
        ->withoutPersisting()
        ->create();

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user->object());
    $userChecker->checkPostAuth($user->object());
})
    ->throws(CustomUserMessageAccountStatusException::class, 'user_empty_account');

\it('throws an exception if user an no enabled account', function () {
    $account = AccountFactory::new(['isEnabled' => false])
        ->withoutPersisting()
        ->create();

    $user = UserFactory::new([
        'isEnabled' => true,
        'accesMarketPlace' => true,
    ])
        ->withoutPersisting()
        ->create();
    $user->addAccount($account->object());

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user->object());
    $userChecker->checkPostAuth($user->object());
})
    ->throws(CustomUserMessageAccountStatusException::class, 'user_disabled');

\it('should not throw an exception if user is enabled, has marketplace access and has ROLE_API role', function () {
    $this->expectNotToPerformAssertions();

    $user = UserFactory::new([
        'isEnabled' => true,
        'accesMarketPlace' => true,
        'roles' => ['ROLE_API'],
    ])
        ->withoutPersisting()
        ->create();

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user->object());
    $userChecker->checkPostAuth($user->object());
});

\it('should not throw an exception if user is enabled, has marketplace and has enabled accounts', function () {
    $this->expectNotToPerformAssertions();

    $account = AccountFactory::new(['isEnabled' => true])
        ->withoutPersisting()
        ->create();

    $user = UserFactory::new([
        'isEnabled' => true,
        'accesMarketPlace' => true,
        'roles' => [],
    ])
        ->withoutPersisting()
        ->create();
    $user->addAccount($account->object());

    $userChecker = new UserChecker();
    $userChecker->checkPreAuth($user->object());
    $userChecker->checkPostAuth($user->object());
});
