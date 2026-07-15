<?php

declare(strict_types=1);

namespace App\DataFixtures\Story;

use App\DataFixtures\Factory\AccountFactory;
use App\DataFixtures\Factory\AdherentFactory;
use App\DataFixtures\Factory\UserFactory;
use Zenstruck\Foundry\Story;

/**
 * @method static UserStory adherentQantis()
 * @method static UserStory adherentMoulinPierre()
 */
final class UserStory extends Story
{
    public function build(): void
    {
        $this->addState('adherentMoulinPierre', AdherentFactory::new()
            ->create([
                'id' => 'ea83cae0-57fa-11ec-9a30-025bcd73cb12',
                'name' => 'LE MOULIN DE PIERRE EURL',
                'channel' => ChannelStory::channelCedap(),
            ]));

        $this->addState('adherentQantis', AdherentFactory::new()
            ->create([
                'id' => 'ce7ed022-5789-11ec-b3a5-0af08f946010',
                'name' => 'GROUPE QANTIS',
                'siret' => '53849238000026',
                'street' => '185 ALLEE DES CYPRES',
                'city' => 'LIMONEST',
                'postalcode' => '69760',
                'country' => 'FRANCE',
                'activiteApe' => 'SERVICE',
                'channel' => ChannelStory::channelQantisAchat(),
            ]));

        $user = UserFactory::createOne([
            'email' => 'gsm@qantis.co',
            'username' => 'gsm@qantis.co',
            'password' => '23AP4DF8',
            'firstName' => 'Gaëtan',
            'lastName' => 'DE SAINTE MARIE',
            'enabled' => true,
        ]);
        AccountFactory::createOne([
            'adherent' => self::adherentQantis(),
            'enabled' => true,
            'phone' => '04 05 06 07 08',
            'serviceFonction' => 'service produits',
            'user' => $user,
        ]);
        AccountFactory::createOne([
            'adherent' => self::adherentMoulinPierre(),
            'enabled' => true,
            'phone' => '04 05 06 07 08',
            'serviceFonction' => 'service produits',
            'user' => $user,
        ]);
        AccountFactory::createOne([
            'adherent' => self::adherentMoulinPierre(),
            'enabled' => true,
            'phone' => '04 05 06 07 08',
            'serviceFonction' => 'service produits',
            'user' => $user,
        ]);
        AccountFactory::createOne([
            'adherent' => self::adherentQantis(),
            'enabled' => true,
            'phone' => '04 05 06 07 08',
            'serviceFonction' => 'service produits',
            'user' => $user,
        ]);

        UserFactory::createOne([
            'email' => 'api_user@qantis.co',
            'username' => 'api_user',
            'password' => '23AP4DF8',
            'firstName' => 'Api',
            'lastName' => 'User',
            'enabled' => true,
            'roles' => ['ROLE_API'],
        ]);
    }
}
