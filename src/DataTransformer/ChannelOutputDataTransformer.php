<?php

declare(strict_types=1);

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\Output\ChannelDesignOutput;
use App\Dto\Output\ChannelDocumentsOutput;
use App\Dto\Output\ChannelOutput;
use App\Entity\Channel;

class ChannelOutputDataTransformer implements DataTransformerInterface
{
    /**
     * @param Channel $object
     */
    public function transform($object, string $to, array $context = []): ChannelOutput
    {
        $channelParameter = $object->getChannelParameter();
        $channelOptions = $object->getChannelOptions();
        $output = new ChannelOutput();
        $output->name = $object->getName();
        $output->id = $object->getId();
        $output->code = $object->getCode();
        $output->hostname = $object->getHostname();

        $output->email = $channelParameter->getEmail();
        $output->phoneNumber = $channelParameter->getPhoneNumber();
        $output->whiteLabel = $channelParameter->isWhiteLabel();

        $channelDesignOutput = new ChannelDesignOutput();
        $channelDesignOutput->primaryColor = $channelParameter->getPrimaryColor();
        $channelDesignOutput->secondaryColor = $channelParameter->getSecondaryColor();
        $channelDesignOutput->textColor = $channelParameter->getTextColor();
        $channelDesignOutput->logo = $channelParameter->getLogo();
        $channelDesignOutput->favicon = $channelParameter->getFavicon();

        $output->design = $channelDesignOutput;

        $channelDocumentsOutput = new ChannelDocumentsOutput();
        $channelDocumentsOutput->generalTermsOfUse = $channelParameter->getGeneralTermsOfUse();
        $channelDocumentsOutput->legalTerms = $channelParameter->getLegalTerms();
        $channelDocumentsOutput->privacyPolicy = $channelParameter->getPrivacyPolicy();

        $output->documents = $channelDocumentsOutput;

        $channelOptionsOutputs = [];
        foreach ($channelOptions as $channelOption) {
            $channelOptionsOutputs[$channelOption->getName()] = $channelOption->getValue();
        }

        $output->options = $channelOptionsOutputs;


        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $data instanceof Channel && $to === ChannelOutput::class;
    }
}
