<?php

declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase as ApiPlatformTestCase;
use App\Tests\Constraints\ResponseEqualsJson;

class ApiTestCase extends ApiPlatformTestCase
{
    public function assertJsonResponseEquals($jsonFilePath, string $message = ''): void
    {
        self::assertThatForResponse(new ResponseEqualsJson($jsonFilePath), $message);
    }
}
