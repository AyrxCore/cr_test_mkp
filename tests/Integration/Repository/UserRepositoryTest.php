<?php

declare(strict_types=1);

use App\DataFixtures\Factory\AccountFactory;
use App\DataFixtures\Factory\AdherentFactory;
use App\DataFixtures\Factory\UserFactory;
use App\Repository\UserRepository;
use App\Tests\Story\Channel\ChannelStory;

\uses()->group('IntegrationUserRepository');

\it('finds a single user by email', function () {
    $user = UserFactory::createOne(['email' => 'jane.doe@example.com']);

    $repository = $this->container->get(UserRepository::class);
    $result = $repository->findUserByUsernameOrEmail('jane.doe@example.com');

    \expect($result?->getId())->toBe($user->getId());
});

\it('finds a single user by username', function () {
    $user = UserFactory::createOne(['username' => 'jane.doe.username']);

    $repository = $this->container->get(UserRepository::class);
    $result = $repository->findUserByUsernameOrEmail('jane.doe.username');

    \expect($result?->getId())->toBe($user->getId());
});

\it('returns null when no user matches', function () {
    $repository = $this->container->get(UserRepository::class);
    $result = $repository->findUserByUsernameOrEmail('unknown@example.com');

    \expect($result)->toBeNull();
});

\it('returns null on empty value', function () {
    $repository = $this->container->get(UserRepository::class);

    \expect($repository->findUserByUsernameOrEmail(''))->toBeNull()
        ->and($repository->findUserByUsernameOrEmail('   '))->toBeNull();
});

\it('does not throw when several users share the same email and picks the one with an enabled account on the channel (MKP-1564)', function () {
    $channel = ChannelStory::channelTest();
    $sharedEmail = 'secretariat@a-mi-bois.fr';

    $adherent = AdherentFactory::createOne(['channel' => $channel]);

    $userWithoutAccount = UserFactory::createOne(['email' => $sharedEmail]);

    $userWithEnabledAccount = UserFactory::createOne(['email' => $sharedEmail]);
    AccountFactory::createOne([
        'user' => $userWithEnabledAccount,
        'adherent' => $adherent,
        'enabled' => true,
    ]);

    $repository = $this->container->get(UserRepository::class);

    $result = $repository->findUserByUsernameOrEmail($sharedEmail, $channel);

    \expect($result?->getId())->toBe($userWithEnabledAccount->getId())
        ->and($result?->getId())->not->toBe($userWithoutAccount->getId());
});

\it('does not throw when several users share the same email and none has an enabled account (fallback, no crash)', function () {
    $channel = ChannelStory::channelTest();
    $sharedEmail = 'duplicate.no.account@example.com';

    UserFactory::createOne(['email' => $sharedEmail]);
    UserFactory::createOne(['email' => $sharedEmail]);

    $repository = $this->container->get(UserRepository::class);

    $result = $repository->findUserByUsernameOrEmail($sharedEmail, $channel);

    \expect($result)->not->toBeNull();
});
