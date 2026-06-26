<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\DBAL\Exception;

trait TestDatabaseCloneTrait
{
    private const string DB_USER = 'postgres';
    private const string DB_PASSWORD = 'password';
    private const string DB_HOST = 'postgres_test_mkp';
    private string $clonedDbName;

    /**
     * @throws Exception
     */
    private function createIsolatedDatabase(): void
    {
        $this->clonedDbName = 'test_'.\uniqid();
        $url = \sprintf('postgresql://%s:%s@%s:5432/%s?serverVersion=17&charset=utf8"', self::DB_USER, self::DB_PASSWORD, self::DB_HOST, $this->clonedDbName);
        $_ENV['DATABASE_URL'] = $url;
        $_SERVER['DATABASE_URL'] = $url;

        $pdo = $this->getPdo();

        $pdo->exec("CREATE DATABASE $this->clonedDbName WITH TEMPLATE template_db_test");
    }

    private function dropIsolatedDatabase(): void
    {
        $pdo = $this->getPdo();

        $sql = <<<SQL
            SELECT pg_terminate_backend(pg_stat_activity.pid)
            FROM pg_stat_activity
            WHERE pg_stat_activity.datname = '$this->clonedDbName'
              AND pid <> pg_backend_pid();
        SQL;

        $pdo->exec($sql);

        $pdo->exec("DROP DATABASE IF EXISTS \"$this->clonedDbName\"");
    }

    private function getPdo(): \PDO
    {
        return new \PDO('pgsql:host='.self::DB_HOST.';port=5432', self::DB_USER, self::DB_PASSWORD, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ]);
    }
}
