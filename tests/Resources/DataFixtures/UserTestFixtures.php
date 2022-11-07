<?php

namespace App\Tests\Resources\DataFixtures;


use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Service\Attribute\Required;


class UserTestFixtures extends Fixture implements OrderedFixtureInterface, FixtureGroupInterface
{

    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UserPasswordHasherInterface $userPasswordHasher;

    /**
     * @param ObjectManager $manager
     * @return void
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $io = new ConsoleOutput();
        $io->writeln("Création d'un user de test ...");

        $user = new User();
        $user->setEmail('test@qantis.co');
        $user->setPassword($this->userPasswordHasher->hashPassword($user,'0000'));
        $manager->persist($user);

        $manager->flush();
    }


    public function getOrder(): int
    {
        return 1;
    }

    public static function getGroups(): array
    {
        return ['test'];
    }


}
