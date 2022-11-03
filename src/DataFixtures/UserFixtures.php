<?php

namespace App\DataFixtures;


use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Service\Attribute\Required;

class UserFixtures extends Fixture implements OrderedFixtureInterface, FixtureGroupInterface
{
    //users à créer
    private const COUNT_USERS = 10;

    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UserPasswordHasherInterface $userPasswordHasher;

    public function load(ObjectManager $manager): void
    {

        $io = new ConsoleOutput();

        $io->writeln("Création d'un utilisateur de test'");

        //création d'un admin
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, '0000'));
        $manager->persist($user);
        $manager->flush();
    }



    public function getOrder(): int
    {
        return 0;
    }

    public static function getGroups(): array
    {
        return ['dev'];
    }
}
