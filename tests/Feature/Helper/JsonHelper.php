<?php

declare(strict_types=1);

namespace App\Tests\Feature\Helper;

class JsonHelper
{
    public static function parseJsonDataFile($path): string
    {
        $jsonFileContent = \file_get_contents(\sprintf('%s/../_data/%s', __DIR__, $path));

        return \json_encode(\json_decode($jsonFileContent));
    }
}
