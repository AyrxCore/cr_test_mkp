<?php

declare(strict_types=1);

use App\Command\Djust\DeleteDjustCartCommand;
use App\Entity\Account;
use App\Entity\Adherent;
use App\Entity\Channel;
use App\Repository\AccountRepository;
use App\Service\CredentialEncryptionService;
use App\Service\Djust\DjustAccountApiService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

uses()->group('DeleteDjustCartCommand', 'djust');

beforeEach(function () {
    $this->accountApi = Mockery::mock(DjustAccountApiService::class);
    $this->accountRepository = Mockery::mock(AccountRepository::class);
    $this->credentialEncryptionService = Mockery::mock(CredentialEncryptionService::class);

    $this->command = new DeleteDjustCartCommand(
        $this->accountApi,
        $this->accountRepository,
        $this->credentialEncryptionService,
    );

    $this->tester = new CommandTester($this->command);
});

afterEach(function () {
    Mockery::close();
});

// --- helpers ---

function makeAccount(
    string $djustUsername = 'user@test.com',
    string $djustPassword = 'encrypted_pass',
    string $customerAccountId = '0000092247',
    string $channelCode = 'QANTIS_ACHAT',
): Account {
    $channel = Mockery::mock(Channel::class);
    $channel->shouldReceive('getCode')->andReturn($channelCode);

    $adherent = Mockery::mock(Adherent::class);
    $adherent->shouldReceive('getChannel')->andReturn($channel);

    $account = Mockery::mock(Account::class);
    $account->shouldReceive('getDjustUsername')->andReturn($djustUsername);
    $account->shouldReceive('getDjustPassword')->andReturn($djustPassword);
    $account->shouldReceive('getDjustCustomerAccountId')->andReturn($customerAccountId);
    $account->shouldReceive('getAdherent')->andReturn($adherent);

    return $account;
}

// --- account not found ---

it('fails when no account is found for the given email', function () {
    $this->accountRepository->shouldReceive('findOneBy')
        ->with(['djustUsername' => 'unknown@test.com'])
        ->andReturn(null);

    $exitCode = $this->tester->execute(['email' => 'unknown@test.com']);

    expect($exitCode)->toBe(Command::FAILURE)
        ->and($this->tester->getDisplay())->toContain('No Djust account found');
});

// --- missing customerAccountId ---

it('fails when account has no djustCustomerAccountId', function () {
    $account = Mockery::mock(Account::class);
    $account->shouldReceive('getDjustCustomerAccountId')->andReturn(null);
    $account->shouldReceive('getDjustUsername')->andReturn('user@test.com');

    $this->accountRepository->shouldReceive('findOneBy')->andReturn($account);

    $exitCode = $this->tester->execute(['email' => 'user@test.com']);

    expect($exitCode)->toBe(Command::FAILURE)
        ->and($this->tester->getDisplay())->toContain('djustCustomerAccountId');
});

// --- missing channel ---

it('fails when account has no channel code', function () {
    $adherent = Mockery::mock(Adherent::class);
    $adherent->shouldReceive('getChannel')->andReturn(null);

    $account = Mockery::mock(Account::class);
    $account->shouldReceive('getDjustCustomerAccountId')->andReturn('0000092247');
    $account->shouldReceive('getDjustUsername')->andReturn('user@test.com');
    $account->shouldReceive('getAdherent')->andReturn($adherent);

    $this->accountRepository->shouldReceive('findOneBy')->andReturn($account);

    $exitCode = $this->tester->execute(['email' => 'user@test.com']);

    expect($exitCode)->toBe(Command::FAILURE)
        ->and($this->tester->getDisplay())->toContain('Cannot resolve channel code');
});

// --- missing djust password ---

it('fails when account has no djust password', function () {
    $channel = Mockery::mock(Channel::class);
    $channel->shouldReceive('getCode')->andReturn('QANTIS_ACHAT');

    $adherent = Mockery::mock(Adherent::class);
    $adherent->shouldReceive('getChannel')->andReturn($channel);

    $account = Mockery::mock(Account::class);
    $account->shouldReceive('getDjustCustomerAccountId')->andReturn('0000092247');
    $account->shouldReceive('getDjustUsername')->andReturn('user@test.com');
    $account->shouldReceive('getDjustPassword')->andReturn(null);
    $account->shouldReceive('getAdherent')->andReturn($adherent);

    $this->accountRepository->shouldReceive('findOneBy')->andReturn($account);

    $exitCode = $this->tester->execute(['email' => 'user@test.com']);

    expect($exitCode)->toBe(Command::FAILURE)
        ->and($this->tester->getDisplay())->toContain('Djust password is not set');
});

// --- auth failure ---

it('fails when Djust authentication throws', function () {
    $account = makeAccount();
    $this->accountRepository->shouldReceive('findOneBy')->andReturn($account);
    $this->credentialEncryptionService->shouldReceive('decrypt')->andReturn('plaintext');
    $this->accountApi->shouldReceive('getAccessToken')
        ->andThrow(new \RuntimeException('Auth failed'));

    $exitCode = $this->tester->execute(['email' => 'user@test.com']);

    expect($exitCode)->toBe(Command::FAILURE)
        ->and($this->tester->getDisplay())->toContain('Failed to authenticate');
});

// --- no open cart ---

it('succeeds with a message when no open cart is found', function () {
    $account = makeAccount();
    $this->accountRepository->shouldReceive('findOneBy')->andReturn($account);
    $this->credentialEncryptionService->shouldReceive('decrypt')->andReturn('plaintext');
    $this->accountApi->shouldReceive('getAccessToken')->andReturn('tok_abc');
    $this->accountApi->shouldReceive('getOpenCarts')->andReturn([]);

    $exitCode = $this->tester->execute(['email' => 'user@test.com']);

    expect($exitCode)->toBe(Command::SUCCESS)
        ->and($this->tester->getDisplay())->toContain('No open cart found');
});

// --- user cancels ---

it('returns success without deleting when user declines confirmation', function () {
    $account = makeAccount();
    $this->accountRepository->shouldReceive('findOneBy')->andReturn($account);
    $this->credentialEncryptionService->shouldReceive('decrypt')->andReturn('plaintext');
    $this->accountApi->shouldReceive('getAccessToken')->andReturn('tok_abc');
    $this->accountApi->shouldReceive('getOpenCarts')->andReturn([
        ['id' => '0000425708', 'reference' => '532-962-1048516', 'productCount' => 2, 'createdAt' => '2026-05-23'],
    ]);
    $this->accountApi->shouldReceive('deleteCart')->never();

    $this->tester->setInputs(['no']);
    $exitCode = $this->tester->execute(['email' => 'user@test.com']);

    expect($exitCode)->toBe(Command::SUCCESS)
        ->and($this->tester->getDisplay())->toContain('Operation cancelled');
});

// --- happy path ---

it('deletes the cart and returns success when confirmed', function () {
    $account = makeAccount();
    $this->accountRepository->shouldReceive('findOneBy')->andReturn($account);
    $this->credentialEncryptionService->shouldReceive('decrypt')->andReturn('plaintext');
    $this->accountApi->shouldReceive('getAccessToken')->andReturn('tok_abc');
    $this->accountApi->shouldReceive('getOpenCarts')->andReturn([
        ['id' => '0000425708', 'reference' => '532-962-1048516', 'productCount' => 2, 'createdAt' => '2026-05-23'],
    ]);
    $this->accountApi->shouldReceive('deleteCart')
        ->once()
        ->with('532-962-1048516', '0000092247', 'tok_abc', 'QANTIS_ACHAT');

    $this->tester->setInputs(['yes']);
    $exitCode = $this->tester->execute(['email' => 'user@test.com']);

    expect($exitCode)->toBe(Command::SUCCESS)
        ->and($this->tester->getDisplay())->toContain('Successfully deleted 1 cart');
});

// --- cart with no reference ---

it('skips cart and warns when reference field is missing', function () {
    $account = makeAccount();
    $this->accountRepository->shouldReceive('findOneBy')->andReturn($account);
    $this->credentialEncryptionService->shouldReceive('decrypt')->andReturn('plaintext');
    $this->accountApi->shouldReceive('getAccessToken')->andReturn('tok_abc');
    $this->accountApi->shouldReceive('getOpenCarts')->andReturn([
        ['id' => '0000425708', 'productCount' => 2, 'createdAt' => '2026-05-23'],
    ]);
    $this->accountApi->shouldReceive('deleteCart')->never();

    $this->tester->setInputs(['yes']);
    $exitCode = $this->tester->execute(['email' => 'user@test.com']);

    // All carts skipped → no error thrown, but warning displayed
    expect($exitCode)->toBe(Command::SUCCESS)
        ->and($this->tester->getDisplay())->toContain('no reference field found');
});

// --- delete failure ---

it('returns failure when deleteCart throws for a cart', function () {
    $account = makeAccount();
    $this->accountRepository->shouldReceive('findOneBy')->andReturn($account);
    $this->credentialEncryptionService->shouldReceive('decrypt')->andReturn('plaintext');
    $this->accountApi->shouldReceive('getAccessToken')->andReturn('tok_abc');
    $this->accountApi->shouldReceive('getOpenCarts')->andReturn([
        ['id' => '0000425708', 'reference' => '532-962-1048516', 'productCount' => 2, 'createdAt' => '2026-05-23'],
    ]);
    $this->accountApi->shouldReceive('deleteCart')
        ->andThrow(new \RuntimeException('Djust API error (HTTP 500): Internal error'));

    $this->tester->setInputs(['yes']);
    $exitCode = $this->tester->execute(['email' => 'user@test.com']);

    expect($exitCode)->toBe(Command::FAILURE)
        ->and($this->tester->getDisplay())->toContain('Some carts could not be deleted');
});
