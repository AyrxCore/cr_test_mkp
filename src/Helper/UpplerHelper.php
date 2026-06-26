<?php

declare(strict_types=1);

namespace App\Helper;

class UpplerHelper
{
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
