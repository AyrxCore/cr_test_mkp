<?php

declare(strict_types=1);

use App\Tests\ApiTestCase;
use App\Tests\IntegrationTestCase;
use App\Tests\UnitTestCase;

\uses(UnitTestCase::class)->in('Unit');
\uses(IntegrationTestCase::class)->in('Integration');
\uses(ApiTestCase::class)->in('Api');
