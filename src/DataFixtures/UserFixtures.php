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

        if ($this->env === 'dev') {
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

            //création d'un utilisateur lié à un seul adhérent
            $user = new User();
            $user->setEmail('m.frebet@qantis.co');
            $user->setUsername('m.frebet@qantis.co');
            $user->setPassword($this->userPasswordHasher->hashPassword($user, '000000'));
            $user->setLastName('FREBET');
            $user->setFirstName('Mélanie');
            $user->setEnabled(true);


            $account = new Account();
            $account->setUpplerUserId(103);
            $account->setUpplerCompanyId(12);
            $account->setUpplerClientId('77_2nm0d8jjbi4g84swkcg80ossc0kwsk8c4wgwowk4g8g0gc048c');
            $account->setUpplerClientSecret('sibr9m87pwgkgccso4cssc80o0cck4s8w80ko4coogwgckwkk');
            $account->setUpplerUsername('m.frebet3');
            $account->setUpplerPassword('000000');
            $account->setUpplerSubAccountId(17);
            $account->setIsEnabled(true);
            $account->setUser($user);

            $manager->persist($user);
            $manager->persist($account);

            //création d'un utilisatur liés à 2 adhérents
            $user = new User();
            $user->setEmail('buyer@qantis.co');
            $user->setUsername('buyer@qantis.co');
            $user->setPassword($this->userPasswordHasher->hashPassword($user, '000000'));
            $user->setLastName('DUPOND');
            $user->setFirstName('Loic');
            $user->setEnabled(false);


            $account = new Account();
            $account->setUpplerUserId(106);
            $account->setUpplerCompanyId(18);
            $account->setUpplerClientId('95_1qd3taz81edc0o4cgc8o8gwcswg8go08s08w4kcsc8okcskwk4');
            $account->setUpplerClientSecret('30uzknniomasoo0wk48o0w00g0ogc4s84gc4wkw00404scw8wg');
            $account->setUpplerSubAccountId(20);
            $account->setIsEnabled(true);
            $account->setUser($user);
            $manager->persist($account);

            $account = new Account();
            $account->setUpplerUserId(107);
            $account->setUpplerCompanyId(14);
            $account->setUpplerClientId('94_6cp0dzzzzckccc0kwwc4w0cw8wo04c8cc0g4sw8s4ooks4o8s8');
            $account->setUpplerClientSecret('4oiwit9r09a8wkogogk8k84wcw8kscgcco0wg8oc0oswo4w08o');
            $account->setUpplerSubAccountId(21);
            $account->setIsEnabled(true);
            $account->setUser($user);
            $manager->persist($account);

            $manager->persist($user);


            $manager->flush();
        } else {
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
