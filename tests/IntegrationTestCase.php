<?php

declare(strict_types=1);

namespace App\Tests;

use App\DataFixtures\Story\ChannelStory;
use App\DataFixtures\Story\UserStory;
use App\Tests\Story\Channel\ChannelParameterStory;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

class IntegrationTestCase extends KernelTestCase
{
    use Factories;
    use TestDatabaseCloneTrait;

    protected ContainerInterface $container;
    protected ?EntityManagerInterface $entityManager = null;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->createIsolatedDatabase();
        // load dev fixtures
        UserStory::load();
        ChannelStory::load();
        ChannelParameterStory::load();

        $kernel = self::bootKernel();
        $this->container = self::getContainer();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->dropIsolatedDatabase();
        $this->entityManager = null;
    }
}
