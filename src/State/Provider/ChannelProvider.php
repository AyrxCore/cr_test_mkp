<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Output\ChannelDesignOutput;
use App\Dto\Output\ChannelDocumentsOutput;
use App\Dto\Output\ChannelOutput;
use App\Entity\Channel;
use App\Repository\ChannelRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class ChannelProvider implements ProviderInterface
{
    public function __construct(private ChannelRepository $channelRepository)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ChannelOutput
    {
        $channel = $this->channelRepository->findOneBy(['hostname' => $uriVariables['hostname']]);

        if (!$channel) {
            throw new NotFoundHttpException('Channel not found');
        }

        return $this->transform($channel);
    }

    public function transform(Channel $channel): ChannelOutput
    {
        $channelParameter = $channel->getChannelParameter();
        $channelOptions = $channel->getChannelOptions();
        $output = new ChannelOutput();
        $output->name = $channel->getName();
        $output->id = $channel->getId();
        $output->code = $channel->getCode();
        $output->hostname = $channel->getHostname();

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
}
