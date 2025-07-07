<?php

declare(strict_types=1);

namespace App\Helper\Formatter;

class PhoneFormatter
{
    private const COUNTRY_CODES = [
        '33' => ['country' => 'FR', 'length' => 2],  // France
        '32' => ['country' => 'BE', 'length' => 2],  // Belgique
        '41' => ['country' => 'CH', 'length' => 2],  // Suisse
        '39' => ['country' => 'IT', 'length' => 2],  // Italie
        '49' => ['country' => 'DE', 'length' => 2],  // Allemagne
        '44' => ['country' => 'GB', 'length' => 2],  // Royaume-Uni
        '34' => ['country' => 'ES', 'length' => 2],  // Espagne
        '351' => ['country' => 'PT', 'length' => 3], // Portugal
        '31' => ['country' => 'NL', 'length' => 2],  // Pays-Bas
        '352' => ['country' => 'LU', 'length' => 3], // Luxembourg
        '43' => ['country' => 'AT', 'length' => 2],  // Autriche
        '353' => ['country' => 'IE', 'length' => 3], // Irlande
    ];

    private const COUNTRY_FORMATS = [
        'FR' => [
            'patterns' => [
                9 => ['/(\d)(\d{2})(\d{2})(\d{2})(\d{2})/' => '$1 $2 $3 $4 $5'],
                'default' => 3,
            ],
            'addZeroPrefix' => true,
        ],
        'BE' => [
            'patterns' => [
                8 => ['/(\d{2})(\d{2})(\d{2})(\d{2})/' => '$1 $2 $3 $4'],
                'default' => ['/(\d{1,2})(.*)/' => '$1 $2'],
            ],
        ],
        'CH' => [
            'patterns' => [
                'default' => ['/(\d{2})(\d{3})(\d{2})(\d{2})/' => '$1 $2 $3 $4'],
            ],
        ],
        'GB' => [
            'patterns' => [
                'default' => ['/(\d{4})(\d{6})/' => '$1 $2'],
            ],
        ],
        'IE' => [
            'patterns' => [
                'default' => ['/(\d{2})(\d{3})(\d{4})/' => '$1 $2 $3'],
            ],
        ],
        'NL' => [
            'patterns' => [
                'default' => ['/(\d{3})(\d{3})(\d{3})/' => '$1 $2 $3'],
            ],
        ],
        'ES' => [
            'patterns' => [
                'default' => ['/(\d{3})(\d{2})(\d{2})(\d{2})/' => '$1 $2 $3 $4'],
            ],
        ],
        'IT' => [
            'patterns' => [
                'default' => ['/(\d{3})(\d{6})/' => '$1 $2'],
            ],
        ],
        'PT' => [
            'patterns' => [
                'default' => ['/(\d{3})(\d{3})(\d{3})/' => '$1 $2 $3'],
            ],
        ],
        'LU' => [
            'patterns' => [
                'default' => ['/(\d{2})(\d{2})(\d{2})/' => '$1 $2 $3'],
            ],
        ],
        'AT' => [
            'patterns' => [
                'default' => ['/(\d{1})(\d{7})/' => '$1 $2'],
            ],
        ],
        'DE' => [
            'patterns' => [
                'default' => 'dynamic',
            ],
            'areaCodes' => [
                '30' => 2, // Berlin
                '40' => 2, // Hambourg
                '89' => 2, // Munich
                '69' => 2, // Francfort
                '11' => 2, // Stuttgart
                '91' => 2,  // Nuremberg
            ],
            'defaultAreaCodeLength' => 3,
        ],
    ];

    private const PLUS_NBSP = "\xE2\x80\x8B+";

    public function format(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $phoneInfo = $this->extractPhoneInfo($phone);
        if (!$phoneInfo) {
            return null;
        }

        if ($phoneInfo['countryCode'] && $phoneInfo['prefix']) {
            return $this->formatInternationalNumber(
                $phoneInfo['countryCode'],
                $phoneInfo['prefix'],
                $phoneInfo['nationalNumber']
            );
        }

        if ($phoneInfo['countryCode'] === null) {
            $nationalNumber = $phoneInfo['nationalNumber'];

            if (\strlen($nationalNumber) === 9) {
                $nationalNumber = '0'.$nationalNumber;
            }

            if (\strlen($nationalNumber) === 10 && \str_starts_with($nationalNumber, '0')) {
                return \preg_replace('/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/', '$1 $2 $3 $4 $5', $nationalNumber);
            }
        }

        return null;
    }

    private function extractPhoneInfo(string $phone): ?array
    {
        $trimmedPhone = \trim($phone);
        $hasPlus = \str_starts_with($trimmedPhone, '+');
        $hasDoubleZero = \str_starts_with($trimmedPhone, '00');

        $number = (string) \preg_replace('/[^0-9]/', '', $phone);

        if ($number === '' || \strlen($number) < 6 || \strlen($number) > 15) {
            return null;
        }

        if ($hasPlus) {
            $result = $this->detectCountryFromNumber($number);
        } elseif ($hasDoubleZero) {
            $numberWithoutPrefix = \substr($number, 2);
            $result = $this->detectCountryFromNumber($numberWithoutPrefix);

            if ($result['countryCode'] === null) {
                return null;
            }

            $hasPlus = true;
        } else {
            $result = $this->detectCountryFromNumber($number);
        }

        if ($result['countryCode'] && \str_starts_with((string) $result['nationalNumber'], '0')) {
            $result['nationalNumber'] = \substr($result['nationalNumber'], 1);
        }

        return $result;
    }

    private function detectCountryFromNumber(string $number): array
    {
        $countryCode = null;
        $prefix = null;
        $nationalNumber = $number;

        foreach (self::COUNTRY_CODES as $code => $data) {
            if (\str_starts_with($number, (string) $code)) {
                $countryCode = $data['country'];
                $prefix = (string) $code;
                $nationalNumber = \substr($number, $data['length']);
                break;
            }
        }

        return [
            'countryCode' => $countryCode,
            'prefix' => $prefix,
            'nationalNumber' => $nationalNumber,
        ];
    }

    private function formatInternationalNumber(string $countryCode, string $prefix, string $nationalNumber): string
    {
        if ($countryCode === 'FR' && isset(self::COUNTRY_FORMATS['FR']['addZeroPrefix']) && \strlen($nationalNumber) === 9) {
            foreach (self::COUNTRY_FORMATS['FR']['patterns'][9] as $pattern => $replacement) {
                return '0'.\preg_replace($pattern, $replacement, $nationalNumber);
            }
        }

        $formattedPrefix = self::PLUS_NBSP.$prefix.' ';

        if (isset(self::COUNTRY_FORMATS[$countryCode])) {
            $format = self::COUNTRY_FORMATS[$countryCode];

            if ($countryCode === 'DE' && $format['patterns']['default'] === 'dynamic') {
                return $this->formatGermanNumber($prefix, $nationalNumber);
            }

            $length = \strlen($nationalNumber);
            if (isset($format['patterns'][$length])) {
                foreach ($format['patterns'][$length] as $pattern => $replacement) {
                    return $formattedPrefix.\preg_replace($pattern, $replacement, $nationalNumber);
                }
            }

            if (isset($format['patterns']['default'])) {
                if (\is_numeric($format['patterns']['default'])) {
                    return $formattedPrefix.\trim(\chunk_split($nationalNumber, $format['patterns']['default'], ' '));
                }

                foreach ($format['patterns']['default'] as $pattern => $replacement) {
                    return $formattedPrefix.\preg_replace($pattern, $replacement, $nationalNumber);
                }
            }
        }

        return $formattedPrefix.\trim(\chunk_split($nationalNumber, 3, ' '));
    }

    private function formatGermanNumber(string $prefix, string $nationalNumber): string
    {
        $format = self::COUNTRY_FORMATS['DE'];
        $areaCodeLength = $this->detectGermanAreaCodeLength($nationalNumber, $format);
        $areaCode = \substr($nationalNumber, 0, $areaCodeLength);
        $rest = \substr($nationalNumber, $areaCodeLength);

        return self::PLUS_NBSP.$prefix.' '.$areaCode.' '.$rest;
    }

    private function detectGermanAreaCodeLength(string $nationalNumber, array $format): int
    {
        $twoDigits = \substr($nationalNumber, 0, 2);
        $threeDigits = \substr($nationalNumber, 0, 3);

        if (isset($format['areaCodes'][$twoDigits])) {
            return $format['areaCodes'][$twoDigits];
        }

        if (isset($format['areaCodes'][$threeDigits])) {
            return $format['areaCodes'][$threeDigits];
        }

        return $format['defaultAreaCodeLength'];
    }
}
