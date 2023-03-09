<?php

namespace App\DataFixtures;

use App\Entity\Adherent;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Uid\Uuid;

class AppFixtures extends Fixture implements FixtureGroupInterface
{

    public function load(ObjectManager $manager): void
    {
        $io = new ConsoleOutput();

        $io->writeln("Création d'adherent de test'");

        $adh = new Adherent();
        $adh->setId(new Uuid('ea83cae0-57fa-11ec-9a30-025bcd73cb12'));
        $adh->setName('LE MOULIN DE PIERRE EURL');
        $manager->persist($adh);


        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['adh'];
    }

}
