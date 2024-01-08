<?php

declare(strict_types=1);

namespace App\Service\Channel;

use App\Entity\Channel;
use App\Entity\ChannelOption;
use Doctrine\ORM\EntityManagerInterface;

class ChannelOptionSynchronizer
{
    public function __construct(private ChannelOptionsBuilder $channelOptionValidator, private EntityManagerInterface $entityManager)
    {
    }

    public function syncChannelOptions(Channel &$channel): void
    {
        $optionsArray = [];
        foreach ($channel->getChannelOptions() as $option) {
            $optionsArray[$option->getName()] = $option->getValue();
        }

        $channelOptions = $this->channelOptionValidator->build($optionsArray);

        $this->updateOptions($channelOptions, $optionsArray, $channel);
        $this->deleteOptions($channelOptions, $optionsArray, $channel);

        $this->entityManager->flush();
    }

    private function updateOptions(array $channelOptions, array $optionsArray, Channel &$channel): void
    {
        foreach ($channelOptions as $key => $value) {
            if (!\array_key_exists($key, $optionsArray)) {
                $channelOption = new ChannelOption();
                $channelOption->setName($key);
                $channelOption->setValue($value);
                $channel->addChannelOption($channelOption);

                $this->entityManager->persist($channelOption);
            }
        }
    }

    private function deleteOptions(array $channelOptions, array $optionsArray, Channel &$channel): void
    {
        foreach ($optionsArray as $key => $value) {
            if (!\array_key_exists($key, $channelOptions)) {
                $channelOption = $this->entityManager->getRepository(ChannelOption::class)->findOneBy(['name' => $key, 'channel' => $channel]);
                $this->entityManager->remove($channelOption);
            }
        }
    }
}
