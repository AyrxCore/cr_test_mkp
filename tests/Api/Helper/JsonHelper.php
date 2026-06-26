<?php

declare(strict_types=1);

namespace App\Tests\Api\Helper;

class JsonHelper
{
    public static function parseJsonDataFile($path): string
    {
        return \json_encode(self::getJsonDataFile($path));
    }

    public static function getJsonDataFile($path): array
    {
        $jsonFileContent = \file_get_contents(\sprintf('%s/../_data/%s', __DIR__, $path));

        return \json_decode($jsonFileContent, true);
    }
}
