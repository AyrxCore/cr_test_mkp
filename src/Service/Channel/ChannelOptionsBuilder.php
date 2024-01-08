<?php

declare(strict_types=1);

namespace App\Service\Channel;

class ChannelOptionsBuilder
{
    private array $validKeys;

    public function __construct(array $validKeys)
    {
        $this->validKeys = $validKeys;
    }

    public function build(array $optionsArray): array
    {
        $keyToRemove = \array_diff(\array_keys($optionsArray), \array_values($this->validKeys));
        foreach ($keyToRemove as $key) {
            unset($optionsArray[$key]);
        }

        $sortedOptionsArray = [];

        foreach ($this->validKeys as $key) {
            if (\array_key_exists($key, $optionsArray)) {
                $sortedOptionsArray[$key] = $optionsArray[$key];
            } else {
                $sortedOptionsArray[$key] = null;
            }
        }

        return $sortedOptionsArray;
    }
}
