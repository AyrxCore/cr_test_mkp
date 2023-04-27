<?php

namespace App\DataFixtures;


use App\Entity\Account;
use App\Entity\Adherent;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;
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

        //création d'un utilisateur test pour l'api
        $user = new User();


        $user->setEmail('test@qantis.co');
        $user->setUsername('test_api');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, '000000'));
        $user->setLastName('TEST');
        $user->setFirstName('api');
        $user->setEnabled(true);
        $user->addRole('ROLE_API');
        $manager->persist($user);
        $manager->flush();

        //création d'un buyer et d'un subaccount

        $adh = new Adherent();
        $adh->setId(new Uuid('ce7ed022-5789-11ec-b3a5-0af08f946010'));
        $adh->setName('GROUPE QANTIS');
        $adh->setReducceCode('KKT26DIB');
        $manager->persist($adh);
        $manager->flush();

        $user = new User();
        $user->setEmail('gsm@qantis.co');
        $user->setUsername('gsm@qantis.co');
        $user->setPassword($this->userPasswordHasher->hashPassword($user, '23AP4DF8'));
        $user->setLastName('DE SAINTE MARIE');
        $user->setFirstName('Gaëtan');
        $user->setEnabled(true);
        $user->setAccesMarketPlace(true);
        $manager->persist($user);
        $manager->flush();

        $account = new Account();
        $account->setUpplerUserId(113);
        $account->setUpplerCompanyId(72);
        $account->setUpplerClientId('101_3l3ueqlt27eog4co400wo0g0kcswg80sk4wocwsgoww4c80ko4');
        $account->setUpplerClientSecret('67nfvkl6q84kck8g4oksww4gsokowgo4s8cw8ow0skk0okw48g');
        $account->setUpplerSubAccountId(23);
        $account->setIsEnabled(true);
        $account->setUser($user);
        $account->setAdherent($adh);
        $manager->persist($account);
        $manager->flush();

        $account = new Account();
        $account->setUpplerUserId(1654);
        $account->setUpplerCompanyId(575);
        $account->setUpplerClientId('2483_5z2ipmbh3dgc0w0gkk4oc4o08g44oc4g4swcs44wsks80oggow');
        $account->setUpplerClientSecret('fq27unvcpb4gs8kswo08so8o044sw4ksos0s44wsks8w004c4');
        $account->setUpplerSubAccountId(867);
        $account->setIsEnabled(true);
        $account->setUser($user);
        $account->setAdherent($adh);
        $manager->persist($account);
        $manager->flush();
        //        }
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
