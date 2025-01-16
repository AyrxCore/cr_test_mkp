<?php

declare(strict_types=1);

use App\Tests\ApiTestCase;
use App\Tests\IntegrationTestCase;
use App\Tests\UnitTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

\uses(
    ApiTestCase::class,
    Factories::class,
    ResetDatabase::class,
)->in('Feature');

\uses(
    UnitTestCase::class,
    Factories::class,
)->in('Unit');

\uses(IntegrationTestCase::class)->in('Integration');
