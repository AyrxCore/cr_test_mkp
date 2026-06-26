<?php

declare(strict_types=1);

namespace App\Service\Djust;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Resyncs Djust customer IDs from the Djust API into Marketplace accounts.
 * Only updates accounts that exist in the target Djust environment; others are left untouched.
 */
readonly class DjustCredentialResyncService
{
    public function __construct(
        private Connection $connection,
        private DjustOperatorApiService $operatorApi,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws InvalidArgumentException
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function fetchDjustPreprodUsers(): array
    {
        return $this->operatorApi->fetchAllCustomerUsers();
    }

    /**
     * @throws Exception
     */
    public function buildLocalEmailIndex(): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT id, LOWER(djust_username) AS email FROM account WHERE djust_username IS NOT NULL'
        );

        $index = [];
        foreach ($rows as $row) {
            $index[$this->stripTestTag($row['email'])] = $row['id'];
        }

        return $index;
    }

    /**
     * Strips the +test tag from an email address.
     * e.g. other+test@example.com → other@example.com.
     */
    public function stripTestTag(string $email): string
    {
        return \preg_replace('/\+test(?=@)/i', '', $email) ?? $email;
    }

    /**
     * @throws \Throwable
     * @throws Exception
     */
    public function updateAccountDjustIdsBatch(array $updates): void
    {
        if (empty($updates)) {
            return;
        }

        $this->connection->beginTransaction();

        try {
            foreach ($updates as $update) {
                $this->connection->executeStatement(
                    'UPDATE account SET
                        djust_customer_user_id    = :djust_customer_user_id,
                        djust_customer_account_id = :djust_customer_account_id,
                        djust_username            = :djust_username,
                        djust_password            = :djust_password
                    WHERE id = :id::uuid',
                    [
                        'djust_customer_user_id'    => $update['djustCustomerUserId'],
                        'djust_customer_account_id' => $update['djustCustomerAccountId'],
                        'djust_username'            => $update['djustUsername'],
                        'djust_password'            => $update['djustPassword'],
                        'id'                        => $update['accountId'],
                    ],
                );
            }

            $this->connection->commit();

            $this->logger->info('[DjustCredentialResync] Batch updated accounts', [
                'count' => \count($updates),
            ]);
        } catch (\Throwable $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }
}
