<?php

declare(strict_types=1);

use App\Service\CredentialEncryptionService;

\beforeEach(function () {
    $this->encryptionKey = 'test-encryption-key-32-characters!';
    $this->service = new CredentialEncryptionService($this->encryptionKey);
});

\it('encrypts data successfully', function () {
    $originalData = 'sensitive-password';

    $encrypted = $this->service->encrypt($originalData);

    \expect($encrypted)->not()->toBe($originalData);
    \expect($encrypted)->toBeString();
    \expect(\strlen($encrypted))->toBeGreaterThan(\strlen($originalData));
})->group('IntegrationCredentialEncryptionServiceTest');

\it('decrypts data successfully', function () {
    $originalData = 'sensitive-password';

    $encrypted = $this->service->encrypt($originalData);
    $decrypted = $this->service->decrypt($encrypted);

    \expect($decrypted)->toBe($originalData);
})->group('IntegrationCredentialEncryptionServiceTest');

\it('encrypts and decrypts empty string', function () {
    $originalData = '';

    $encrypted = $this->service->encrypt($originalData);
    $decrypted = $this->service->decrypt($encrypted);

    \expect($decrypted)->toBe($originalData);
})->group('IntegrationCredentialEncryptionServiceTest');

\it('encrypts and decrypts special characters', function () {
    $originalData = 'pàssw0rd!@#$%^&*()_+{}|:<>?[]\\;\'",./`~';

    $encrypted = $this->service->encrypt($originalData);
    $decrypted = $this->service->decrypt($encrypted);

    \expect($decrypted)->toBe($originalData);
})->group('IntegrationCredentialEncryptionServiceTest');

\it('encrypts and decrypts unicode characters', function () {
    $originalData = 'motdepasse123éàèç🔒';

    $encrypted = $this->service->encrypt($originalData);
    $decrypted = $this->service->decrypt($encrypted);

    \expect($decrypted)->toBe($originalData);
})->group('IntegrationCredentialEncryptionServiceTest');

\it('throw Exception if password if encrypt with A key and decrypt with B key', function () {
    $originalData = 'test-password';
    $differentKey = 'different-key';

    $service1 = new CredentialEncryptionService($this->encryptionKey);
    $service2 = new CredentialEncryptionService($differentKey);

    $originalKeyEncrypted = $service1->encrypt($originalData);
    $service2->decrypt($originalKeyEncrypted);
})->group('IntegrationCredentialEncryptionServiceTest')->throws(Exception::class);
