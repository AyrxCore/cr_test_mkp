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
    public function build(): void
    {
        // TODO: create test user fixtures
        DevUserStory::load();

        $this->addState('adherentQantisTest', AdherentFactory::new()
            ->create([
                'name' => 'GROUPE QANTIS TEST',
                'reducceCode' => 'KKT26DIB',
                'channel' => ChannelStory::channelTest(),
            ]));

        $user = UserFactory::find(['username' => 'gsm@qantis.co']);
        $account = AccountFactory::new()
            ->create([
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

        $user->addAccount($account->object());
    }
}
