<?php

namespace App\DataFixtures;


use App\Entity\Account;
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
        $user->setEmail('m.frebet@qantis.co');
        $user->setUsername('m.frebet@qantis.co');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, '000000'));
        $user->setLastName('FREBET');
        $user->setFirstName('Mélanie');
        $user->setEnabled(true);


        $account = new Account();
        $account->setUpplerUserId(103);
        $account->setUpplerClientId(31);
        $account->setUpplerCompanyId(12);
        $account->setUpplerUsername('m.frebet3');
        $account->setUpplerPassword('000000');
        $account->setUpplerSubAccountId(17);
        $account->setUser($user);

        $manager->persist($user);
        $manager->persist($account);

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
