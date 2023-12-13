<?php

declare(strict_types=1);

namespace App\Twig;

use App\Constants\Defaults;
use App\Entity\Channel;
use App\Helper\ColorHelper;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Psr\Log\LoggerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ChannelExtension extends AbstractExtension
{
    private PhoneNumberUtil $phoneUtil;

    public function __construct(private LoggerInterface $logger)
    {
        $this->phoneUtil = PhoneNumberUtil::getInstance();
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('channel_primary_color', [$this, 'getChannelPrimaryColor']),
            new TwigFunction('channel_secondary_color', [$this, 'getChannelSecondaryColor']),
            new TwigFunction('channel_text_color', [$this, 'getChannelTextColor']),
            new TwigFunction('channel_body_background', [$this, 'getChannelBodyBackground']),
            new TwigFunction('channel_phone_number', [$this, 'getChannelPhoneNumber']),
            new TwigFunction('channel_formatted_phone_number', [$this, 'getChannelFormattedPhoneNumber']),
            new TwigFunction('channel_email', [$this, 'getChannelEmail']),
        ];
    }

    public function getChannelFavicon(Channel $channel): string
    {
        return $channel->getChannelParameter()->getFavicon();
    }

    public function getChannelPrimaryColor(Channel $channel): string
    {
        return ColorHelper::hexToCssRgb($channel->getChannelParameter()->getPrimaryColor());
    }

    public function getChannelSecondaryColor(Channel $channel): string
    {
        return ColorHelper::hexToCssRgb($channel->getChannelParameter()->getSecondaryColor());
    }

    public function getChannelTextColor(Channel $channel): string
    {
        return ColorHelper::hexToCssRgb($channel->getChannelParameter()->getTextColor());
    }

    public function getChannelBodyBackground(Channel $channel): string
    {
        return ColorHelper::hexToCssRgb($channel->getChannelParameter()->getPrimaryColor(), 0.05);
    }

    public function getChannelPhoneNumber(Channel $channel): string
    {
        return $channel->getChannelParameter()->getPhoneNumber() ?? Defaults::DEFAULT_CHANNEL_PHONE_NUMBER;
    }

    public function getChannelFormattedPhoneNumber(Channel $channel, $format = PhoneNumberFormat::NATIONAL): string
    {
        $phoneNumber = $this->getChannelPhoneNumber($channel);

        try {
            // TODO: at the moment we are assuming all phone numbers are French. If we ever open internationally we will need to get the correct country code.
            $phoneNumberProto = $this->phoneUtil->parse($phoneNumber, 'FR');
        } catch (NumberParseException $exception) {
            $this->logger->warning('Unable to parse phone number', [
                'phoneNumber' => $phoneNumber,
                'exception' => (string) $exception,
            ]);

            return $phoneNumber;
        }

        return $this->phoneUtil->format($phoneNumberProto, $format);
    }

    public function getChannelEmail(Channel $channel): string
    {
        return $channel->getChannelParameter()->getEmail() ?? Defaults::DEFAULT_CHANNEL_EMAIL;
    }
}
