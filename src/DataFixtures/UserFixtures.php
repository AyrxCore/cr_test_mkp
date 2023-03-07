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

    private string $env;

    public function __construct(string $env)
    {
        $this->env = $env;
    }

    //users à créer
    private const COUNT_USERS = 10;

    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UserPasswordHasherInterface $userPasswordHasher;

    public function load(ObjectManager $manager): void
    {
        $io = new ConsoleOutput();

        $io->writeln("Création d'utilisateurs de test sur Uppler '.$this->env");


        $user = new User();
        $user->setEmail('gsm@qantis.co');
        $user->setUsername('gsm@qantis.co');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, '23AP4DF8'));
        $user->setLastName('DE SAINTE MARIE');
        $user->setFirstName('Gaëtan');
        $user->setEnabled(true);

        $account = new Account();
        $account->setUpplerUserId(113);
        $account->setUpplerCompanyId(72);
        $account->setUpplerClientId('101_3l3ueqlt27eog4co400wo0g0kcswg80sk4wocwsgoww4c80ko4');
        $account->setUpplerClientSecret('67nfvkl6q84kck8g4oksww4gsokowgo4s8cw8ow0skk0okw48g');
        $account->setUpplerSubAccountId(23);
        $account->setIsEnabled(true);
        $account->setUser($user);
        $manager->persist($account);

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
