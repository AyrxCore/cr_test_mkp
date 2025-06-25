<?php

declare(strict_types=1);

namespace App\Helper;

class UpplerHelper
{
    public static function getOrderNumber(mixed $upplerResponse): ?string
    {
        foreach ($upplerResponse['numbers'] as $number) {
            if ($number['type'] === 'order') {
                return $number['number'];
            }
        }

        return null;
    }

    public static function formatPrice(int $price): float
    {
        return \round($price * 0.01, 2);
    }

    public static function getAllPaginatedResults(callable $fetchPage, int $perPage = 200): array
    {
        $allResults = [];
        $page = 1;
        $hasMoreResults = true;

        while ($hasMoreResults) {
            $response = $fetchPage($perPage, $page);

            if (empty($response['results'])) {
                $hasMoreResults = false;
                continue;
            }

            $allResults = \array_merge($allResults, $response['results']);

            if (\count($response['results']) < $perPage) {
                $hasMoreResults = false;
            }

            ++$page;
        }

        return $allResults;
    }
}
