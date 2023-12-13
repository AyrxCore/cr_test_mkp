<?php

declare(strict_types=1);

namespace App\DataFixtures\Story;

use App\DataFixtures\Factory\ChannelFactory;
use App\DataFixtures\Factory\ChannelParameterFactory;
use Doctrine\Inflector\InflectorFactory;
use Zenstruck\Foundry\Story;

/**
 * @method static ChannelStory channelQantisAchat()
 * @method static ChannelStory channelCedap()
 * @method static ChannelStory channelOpteam()
 * @method static ChannelStory channelFspf()
 * @method static ChannelStory channelUnep()
 * @method static ChannelStory channelCaper()
 * @method static ChannelStory channelFnphp()
 * @method static ChannelStory channelVetinweb()
 * @method static ChannelStory channelFfp()
 * @method static ChannelStory channelSyneg()
 * @method static ChannelStory channelBatiman()
 * @method static ChannelStory channelDlr()
 * @method static ChannelStory channelUnge()
 * @method static ChannelStory channelArtema()
 * @method static ChannelStory channelCdl()
 * @method static ChannelStory channelQachatspublics()
 * @method static ChannelStory channelUits()
 */
class ChannelStory extends Story
{
    public function __construct(private array $channels)
    {
    }

    public function build(): void
    {
        $inflector = InflectorFactory::create()->build();

        foreach ($this->channels as $channel) {
            $channelParameter = $channel['channelParameter'];
            unset($channel['channelParameter']);
            // store channel in a state with a camelized key "channelCode" (ex: channelQantisAchat)
            $channelName = $inflector->camelize(\sprintf('channel_%s', \strtolower($channel['code'])));
            $createdChannel = ChannelFactory::new()->create($channel);
            $this->addState(
                $channelName,
                $createdChannel
            );

            $channelParameter['channel'] = $createdChannel;
            ChannelParameterFactory::new()->create($channelParameter);
        }
    }
}
