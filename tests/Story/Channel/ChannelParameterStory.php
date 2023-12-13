<?php

declare(strict_types=1);

namespace App\Tests\Story\Channel;

use App\DataFixtures\Factory\ChannelParameterFactory;
use Zenstruck\Foundry\Story;

/**
 * @method static ChannelStory channelTest()
 */
final class ChannelParameterStory extends Story
{
    public function build(): void
    {
        ChannelParameterFactory::new()->create([
            'email' => 'test@qantis.co',
            'phoneNumber' => '+33123456789',
            'logo' => 'https://path.to-logo.com/logo.png',
            'favicon' => 'https://path.to-favicon.com/favicon.png',
            'legalTerms' => 'https://legal-terms.qantis.co',
            'generalTermsOfUse' => 'https://general-terms-of-use.qantis.co',
            'privacyPolicy' => 'https://privacy-policy.qantis.co',
            'primaryColor' => '#FF0000',
            'secondaryColor' => '#FFFFFF',
            'textColor' => '#000000',
            'whiteLabel' => false,
            'channel' => ChannelStory::channelTest(),
        ]);
    }
}
