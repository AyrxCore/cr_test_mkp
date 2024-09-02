<?php

declare(strict_types=1);

namespace App\Tests\Story\LogAutoLoginError;

use App\DataFixtures\Factory\LogAutoLoginErrorFactory;
use Zenstruck\Foundry\Story;

class LogAutoLoginErrorStory extends Story
{
    public function build(): void
    {
        LogAutoLoginErrorFactory::createMany(50);
    }
}
