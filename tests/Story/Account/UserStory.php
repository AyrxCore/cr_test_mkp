<?php

declare(strict_types=1);

namespace App\Tests\Story\Account;

use App\DataFixtures\Factory\AccountFactory;
use App\DataFixtures\Factory\AdherentFactory;
use App\DataFixtures\Factory\UserFactory;
use App\DataFixtures\Story\UserStory as DevUserStory;
use App\Tests\Story\Channel\ChannelStory;
use Zenstruck\Foundry\Story;

/**
 * @method static UserStory adherentQantisTest()
 */
final class UserStory extends Story
{
    public const string DEFAULT_USER = 'test@qantis.co';
    public const string DEFAULT_PASSWORD = '23AP4DF8';

    public function build(): void
    {
        // TODO: create test user fixtures
        DevUserStory::load();

        $this->addState('adherentQantisTest', AdherentFactory::new()
            ->create([
                'street' => '185 ALLEE DES CYPRES',
                'city' => 'LIMONEST',
                'postalcode' => '69760',
                'country' => 'FRANCE',
                'activiteApe' => 'SERVICE',
                'name' => 'GROUPE QANTIS TEST',
                'siret' => '53849238000026',
                'channel' => ChannelStory::channelTest(),
            ]));

        $user = UserFactory::createOne([
            'email' => self::DEFAULT_USER,
            'username' => self::DEFAULT_USER,
            'password' => self::DEFAULT_PASSWORD,
            'firstName' => 'Gaëtan',
            'lastName' => 'DE SAINTE MARIE',
            'enabled' => true,
        ]);

        AccountFactory::createOne([
            'user' => $user,
            'upplerClientId' => '101_3l3ueqlt27eog4co400wo0g0kcswg80sk4wocwsgoww4c80ko4',
            'upplerClientSecret' => '67nfvkl6q84kck8g4oksww4gsokowgo4s8cw8ow0skk0okw48g',
            'upplerUserId' => '113',
            'upplerSubAccountId' => '23',
            'upplerCompanyId' => '72',
            'adherent' => self::adherentQantisTest(),
            'enabled' => true,
            'phone' => '04 05 06 07 08',
        ]);
    }
}
