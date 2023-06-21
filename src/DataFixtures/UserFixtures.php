<?php

namespace App\DataFixtures;

use App\DataFixtures\Story\UserStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function __construct(private UserStory $usersStory)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->usersStory::load();
    }
}
