<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataFixtures\Story\ChannelStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ChannelFixtures extends Fixture
{
    public function __construct(private ChannelStory $channelStory)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->channelStory::load();
    }
}
