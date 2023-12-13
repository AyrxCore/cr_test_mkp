<?php

declare(strict_types=1);

namespace App\Tests\Story\Channel;

use App\DataFixtures\Factory\ChannelFactory;
use Zenstruck\Foundry\Story;

/**
 * @method static ChannelStory channelTest()
 */
final class ChannelStory extends Story
{
    public function build(): void
    {
        $this->addState(
            'channelTest',
            ChannelFactory::new()->create([
                'name' => 'QANTIS Test',
                'code' => 'QANTIS_TEST',
                'hostname' => 'https://test.qantis.co',
            ])
        );
    }
}
