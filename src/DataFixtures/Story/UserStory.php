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
            'upplerClientId' => '101_3l3ueqlt27eog4co400wo0g0kcswg80sk4wocwsgoww4c80ko4',
            'upplerClientSecret' => '67nfvkl6q84kck8g4oksww4gsokowgo4s8cw8ow0skk0okw48g',
            'upplerUserId' => '113',
            'upplerSubAccountId' => '23',
            'upplerCompanyId' => '72',
            'adherent' => self::adherentQantis(),
            'enabled' => true,
            'phone' => '04 05 06 07 08',
            'serviceFonction' => 'service produits',
            'user' => $user,
        ]);
        AccountFactory::createOne([
            'upplerClientId' => '101_3l3ueqlt27eog4co400wo0g0kcswg80sk4wocwsgoww4c80ko4',
            'upplerClientSecret' => '67nfvkl6q84kck8g4oksww4gsokowgo4s8cw8ow0skk0okw48g',
            'upplerUserId' => '113',
            'upplerSubAccountId' => '23',
            'upplerCompanyId' => '72',
            'adherent' => self::adherentMoulinPierre(),
            'enabled' => true,
            'phone' => '04 05 06 07 08',
            'serviceFonction' => 'service produits',
            'user' => $user,
        ]);
        AccountFactory::createOne([
            'upplerClientId' => '2483_5z2ipmbh3dgc0w0gkk4oc4o08g44oc4g4swcs44wsks80oggow',
            'upplerClientSecret' => 'fq27unvcpb4gs8kswo08so8o044sw4ksos0s44wsks8w004c4',
            'upplerUserId' => '1654',
            'upplerSubAccountId' => '867',
            'upplerCompanyId' => '575',
            'adherent' => self::adherentMoulinPierre(),
            'enabled' => true,
            'phone' => '04 05 06 07 08',
            'serviceFonction' => 'service produits',
            'user' => $user,
        ]);
        AccountFactory::createOne([
            'upplerClientId' => '2483_5z2ipmbh3dgc0w0gkk4oc4o08g44oc4g4swcs44wsks80oggow',
            'upplerClientSecret' => 'fq27unvcpb4gs8kswo08so8o044sw4ksos0s44wsks8w004c4',
            'upplerUserId' => '1654',
            'upplerSubAccountId' => '867',
            'upplerCompanyId' => '575',
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
