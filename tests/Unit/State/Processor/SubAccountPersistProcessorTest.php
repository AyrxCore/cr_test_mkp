<?php

declare(strict_types=1);

use ApiPlatform\Metadata\Patch;
use App\Dto\SubAccount;
use App\Entity\Account;
use App\Entity\User;
use App\Repository\AccountRepository;
use App\Repository\ChannelRepository;
use App\Repository\UserInfoUpdateRequestRepository;
use App\State\Processor\SubAccountPersistProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Uid\Uuid;

\uses()->group('UnitSubAccountPersistProcessor', 'SubAccount');

\beforeEach(function () {
    $this->accountRepository = Mockery::mock(AccountRepository::class);
    $this->channelRepository = Mockery::mock(ChannelRepository::class);
    $this->em = Mockery::mock(EntityManagerInterface::class);
    $this->eventDispatcher = Mockery::mock(EventDispatcherInterface::class);
    $this->requestStack = Mockery::mock(RequestStack::class);
    $this->userInfoUpdateRequestRepository = Mockery::mock(UserInfoUpdateRequestRepository::class);

    $this->processor = new SubAccountPersistProcessor(
        $this->accountRepository,
        $this->channelRepository,
        $this->em,
        $this->eventDispatcher,
        $this->requestStack,
        $this->userInfoUpdateRequestRepository
    );
});

\afterEach(function () {
    Mockery::close();
});

\it('processes lastName change request', function () {
    $user = Mockery::mock(User::class);
    $user->shouldReceive('getLastName')->andReturn('OldLastName', 'NewLastName');
    $user->shouldReceive('getFirstName')->andReturn('John');
    $user->shouldReceive('getEmail')->andReturn('test@example.com');
    $user->shouldReceive('setLastName')->with('NewLastName')->once();

    $account = Mockery::mock(Account::class);
    $account->shouldReceive('getUser')->andReturn($user);
    $account->shouldReceive('getPhone')->andReturn('01 23 45 67 89');

    $subAccount = new SubAccount();
    $subAccount->setAccountId(Uuid::v4());
    $subAccount->setId(123);
    $subAccount->setLastName('NewLastName');

    $this->accountRepository
        ->shouldReceive('find')
        ->once()
        ->andReturn($account);

    $this->userInfoUpdateRequestRepository
        ->shouldReceive('findOneBy')
        ->once()
        ->andReturn(null);

    $this->em
        ->shouldReceive('persist')
        ->twice();

    $this->em
        ->shouldReceive('flush')
        ->once();

    $this->eventDispatcher
        ->shouldReceive('dispatch')
        ->once();

    $result = $this->processor->process($subAccount, new Patch());

    \expect($result)->toBeInstanceOf(SubAccount::class)
        ->and($result->getId())->toBe(123)
        ->and($result->getLastName())->toBe('NewLastName')
        ->and($result->getFirstName())->toBe('John')
        ->and($result->getPhone())->toBe('01 23 45 67 89');
});

\it('processes firstName change request', function () {
    $user = Mockery::mock(User::class);
    $user->shouldReceive('getFirstName')->andReturn('OldFirstName', 'NewFirstName');
    $user->shouldReceive('getLastName')->andReturn('Doe');
    $user->shouldReceive('getEmail')->andReturn('test@example.com');
    $user->shouldReceive('setFirstName')->with('NewFirstName')->once();

    $account = Mockery::mock(Account::class);
    $account->shouldReceive('getUser')->andReturn($user);
    $account->shouldReceive('getPhone')->andReturn('01 23 45 67 89');

    $subAccount = new SubAccount();
    $subAccount->setAccountId(Uuid::v4());
    $subAccount->setId(123);
    $subAccount->setFirstName('NewFirstName');

    $this->accountRepository
        ->shouldReceive('find')
        ->once()
        ->andReturn($account);

    $this->userInfoUpdateRequestRepository
        ->shouldReceive('findOneBy')
        ->once()
        ->andReturn(null);

    $this->em
        ->shouldReceive('persist')
        ->twice();

    $this->em
        ->shouldReceive('flush')
        ->once();

    $this->eventDispatcher
        ->shouldReceive('dispatch')
        ->once();

    $result = $this->processor->process($subAccount, new Patch());

    \expect($result)->toBeInstanceOf(SubAccount::class)
        ->and($result->getId())->toBe(123)
        ->and($result->getFirstName())->toBe('NewFirstName')
        ->and($result->getLastName())->toBe('Doe')
        ->and($result->getPhone())->toBe('01 23 45 67 89');
});

\it('processes phone change request', function () {
    $user = Mockery::mock(User::class);
    $user->shouldReceive('getFirstName')->andReturn('John');
    $user->shouldReceive('getLastName')->andReturn('Doe');
    $user->shouldReceive('getEmail')->andReturn('test@example.com');

    $account = Mockery::mock(Account::class);
    $account->shouldReceive('getUser')->andReturn($user);
    $account->shouldReceive('getPhone')->andReturn('01 23 45 67 89', '04 78 12 34 56');
    $account->shouldReceive('setPhone')->with('04 78 12 34 56')->once();

    $subAccount = new SubAccount();
    $subAccount->setAccountId(Uuid::v4());
    $subAccount->setId(123);
    $subAccount->setPhone('0478123456');

    $this->accountRepository
        ->shouldReceive('find')
        ->once()
        ->andReturn($account);

    $this->userInfoUpdateRequestRepository
        ->shouldReceive('findOneBy')
        ->once()
        ->andReturn(null);

    $this->em
        ->shouldReceive('persist')
        ->twice();

    $this->em
        ->shouldReceive('flush')
        ->once();

    $this->eventDispatcher
        ->shouldReceive('dispatch')
        ->once();

    $result = $this->processor->process($subAccount, new Patch());

    \expect($result)->toBeInstanceOf(SubAccount::class)
        ->and($result->getId())->toBe(123)
        ->and($result->getPhone())->toBe('04 78 12 34 56')
        ->and($result->getFirstName())->toBe('John')
        ->and($result->getLastName())->toBe('Doe');
});

\it('formats phone number with spaces before saving', function () {
    $user = Mockery::mock(User::class);
    $user->shouldReceive('getFirstName')->andReturn('John');
    $user->shouldReceive('getLastName')->andReturn('Doe');
    $user->shouldReceive('getEmail')->andReturn('test@example.com');

    $account = Mockery::mock(Account::class);
    $account->shouldReceive('getUser')->andReturn($user);
    $account->shouldReceive('getPhone')->andReturn('01 23 45 67 89', '04 78 12 34 56');
    $account->shouldReceive('setPhone')->with('04 78 12 34 56')->once();

    $subAccount = new SubAccount();
    $subAccount->setAccountId(Uuid::v4());
    $subAccount->setId(123);
    $subAccount->setPhone('0478123456'); // Input without spaces

    $this->accountRepository
        ->shouldReceive('find')
        ->once()
        ->andReturn($account);

    $this->userInfoUpdateRequestRepository
        ->shouldReceive('findOneBy')
        ->once()
        ->andReturn(null);

    $this->em
        ->shouldReceive('persist')
        ->twice();

    $this->em
        ->shouldReceive('flush')
        ->once();

    $this->eventDispatcher
        ->shouldReceive('dispatch')
        ->once();

    $result = $this->processor->process($subAccount, new Patch());

    \expect($result)->toBeInstanceOf(SubAccount::class)
        ->and($result->getPhone())->toBe('04 78 12 34 56');
});

\it('returns stdClass instance after processing', function () {
    $user = Mockery::mock(User::class);
    $user->shouldReceive('getLastName')->andReturn('OldName', 'NewName');
    $user->shouldReceive('getFirstName')->andReturn('John');
    $user->shouldReceive('getEmail')->andReturn('test@example.com');
    $user->shouldReceive('setLastName')->with('NewName')->once();

    $account = Mockery::mock(Account::class);
    $account->shouldReceive('getUser')->andReturn($user);
    $account->shouldReceive('getPhone')->andReturn('01 23 45 67 89');

    $subAccount = new SubAccount();
    $subAccount->setAccountId(Uuid::v4());
    $subAccount->setId(123);
    $subAccount->setLastName('NewName');

    $this->accountRepository
        ->shouldReceive('find')
        ->once()
        ->andReturn($account);

    $this->userInfoUpdateRequestRepository
        ->shouldReceive('findOneBy')
        ->once()
        ->andReturn(null);

    $this->em
        ->shouldReceive('persist')
        ->twice();

    $this->em
        ->shouldReceive('flush')
        ->once();

    $this->eventDispatcher
        ->shouldReceive('dispatch')
        ->once();

    $result = $this->processor->process($subAccount, new Patch());

    \expect($result)->toBeInstanceOf(SubAccount::class)
        ->and($result->getId())->toBe(123)
        ->and($result->getLastName())->toBe('NewName')
        ->and($result->getFirstName())->toBe('John')
        ->and($result->getPhone())->toBe('01 23 45 67 89');
});

\it('formats +33 phone number with spaces', function () {
    $user = Mockery::mock(User::class);
    $user->shouldReceive('getFirstName')->andReturn('John');
    $user->shouldReceive('getLastName')->andReturn('Doe');
    $user->shouldReceive('getEmail')->andReturn('test@example.com');

    $account = Mockery::mock(Account::class);
    $account->shouldReceive('getUser')->andReturn($user);
    $account->shouldReceive('getPhone')->andReturn('', '+33 4 78 12 34 56');
    $account->shouldReceive('setPhone')->with('+33 4 78 12 34 56')->once();

    $subAccount = new SubAccount();
    $subAccount->setAccountId(Uuid::v4());
    $subAccount->setId(123);
    $subAccount->setPhone('+33478123456');

    $this->accountRepository
        ->shouldReceive('find')
        ->once()
        ->andReturn($account);

    $this->userInfoUpdateRequestRepository
        ->shouldReceive('findOneBy')
        ->once()
        ->andReturn(null);

    $this->em
        ->shouldReceive('persist')
        ->twice();

    $this->em
        ->shouldReceive('flush')
        ->once();

    $this->eventDispatcher
        ->shouldReceive('dispatch')
        ->once();

    $result = $this->processor->process($subAccount, new Patch());

    \expect($result)->toBeInstanceOf(SubAccount::class)
        ->and($result->getPhone())->toBe('+33 4 78 12 34 56')
        ->and($result->getFirstName())->toBe('John')
        ->and($result->getLastName())->toBe('Doe');
});

\it('preserves already formatted phone number', function () {
    $user = Mockery::mock(User::class);
    $user->shouldReceive('getFirstName')->andReturn('John');
    $user->shouldReceive('getLastName')->andReturn('Doe');
    $user->shouldReceive('getEmail')->andReturn('test@example.com');

    $account = Mockery::mock(Account::class);
    $account->shouldReceive('getUser')->andReturn($user);
    $account->shouldReceive('getPhone')->andReturn('04 78 12 34 56');

    $subAccount = new SubAccount();
    $subAccount->setAccountId(Uuid::v4());
    $subAccount->setId(123);
    $subAccount->setPhone('04 78 12 34 56');

    $this->accountRepository
        ->shouldReceive('find')
        ->once()
        ->andReturn($account);

    $this->em
        ->shouldReceive('persist')
        ->once();

    $this->em
        ->shouldReceive('flush')
        ->once();

    $this->eventDispatcher
        ->shouldReceive('dispatch')
        ->once();

    $result = $this->processor->process($subAccount, new Patch());

    \expect($result)->toBeInstanceOf(SubAccount::class)
        ->and($result->getPhone())->toBe('04 78 12 34 56')
        ->and($result->getFirstName())->toBe('John')
        ->and($result->getLastName())->toBe('Doe');
});

\it('preserves international phone number with + and spaces', function () {
    $user = Mockery::mock(User::class);
    $user->shouldReceive('getFirstName')->andReturn('John');
    $user->shouldReceive('getLastName')->andReturn('Doe');
    $user->shouldReceive('getEmail')->andReturn('test@example.com');

    $account = Mockery::mock(Account::class);
    $account->shouldReceive('getUser')->andReturn($user);
    $account->shouldReceive('getPhone')->andReturn('', '+352 27 86 14 50');
    $account->shouldReceive('setPhone')->with('+352 27 86 14 50')->once();

    $subAccount = new SubAccount();
    $subAccount->setAccountId(Uuid::v4());
    $subAccount->setId(123);
    $subAccount->setPhone('+352 27 86 14 50');

    $this->accountRepository
        ->shouldReceive('find')
        ->once()
        ->andReturn($account);

    $this->userInfoUpdateRequestRepository
        ->shouldReceive('findOneBy')
        ->once()
        ->andReturn(null);

    $this->em
        ->shouldReceive('persist')
        ->twice();

    $this->em
        ->shouldReceive('flush')
        ->once();

    $this->eventDispatcher
        ->shouldReceive('dispatch')
        ->once();

    $result = $this->processor->process($subAccount, new Patch());

    \expect($result)->toBeInstanceOf(SubAccount::class)
        ->and($result->getPhone())->toBe('+352 27 86 14 50')
        ->and($result->getFirstName())->toBe('John')
        ->and($result->getLastName())->toBe('Doe');
});

\it('preserves international phone number with + formatted by user', function () {
    $user = Mockery::mock(User::class);
    $user->shouldReceive('getFirstName')->andReturn('John');
    $user->shouldReceive('getLastName')->andReturn('Doe');
    $user->shouldReceive('getEmail')->andReturn('test@example.com');

    $account = Mockery::mock(Account::class);
    $account->shouldReceive('getUser')->andReturn($user);
    $account->shouldReceive('getPhone')->andReturn('', '+32 4 246 00 98');
    $account->shouldReceive('setPhone')->with('+32 4 246 00 98')->once();

    $subAccount = new SubAccount();
    $subAccount->setAccountId(Uuid::v4());
    $subAccount->setId(123);
    $subAccount->setPhone('+32 4 246 00 98');

    $this->accountRepository
        ->shouldReceive('find')
        ->once()
        ->andReturn($account);

    $this->userInfoUpdateRequestRepository
        ->shouldReceive('findOneBy')
        ->once()
        ->andReturn(null);

    $this->em
        ->shouldReceive('persist')
        ->twice();

    $this->em
        ->shouldReceive('flush')
        ->once();

    $this->eventDispatcher
        ->shouldReceive('dispatch')
        ->once();

    $result = $this->processor->process($subAccount, new Patch());

    \expect($result)->toBeInstanceOf(SubAccount::class)
        ->and($result->getPhone())->toBe('+32 4 246 00 98')
        ->and($result->getFirstName())->toBe('John')
        ->and($result->getLastName())->toBe('Doe');
});
