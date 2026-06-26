<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CredentialEncryptionService
{
    private const string CYPHER_ALGORITHM = 'AES-256-CBC';

    public function __construct(
        #[Autowire('%app.credential_encryption_key%')]
        private readonly string $encryptionKey,
    ) {
    }

    public function encrypt(string $data): string
    {
        $iv = \random_bytes(16);
        $encrypted = \openssl_encrypt($data, self::CYPHER_ALGORITHM, $this->encryptionKey, 0, $iv);

        return \base64_encode($iv.$encrypted);
    }

    public function decrypt(string $encryptedData): string
    {
        $data = \base64_decode($encryptedData, true);

        $iv = \substr($data, 0, 16);
        $encrypted = \substr($data, 16);

        $decrypt = \openssl_decrypt($encrypted, self::CYPHER_ALGORITHM, $this->encryptionKey, 0, $iv);

        if ($decrypt === false) {
            throw new \Exception('Unable to decrypt data');
        }

        return $decrypt;
    }
}
