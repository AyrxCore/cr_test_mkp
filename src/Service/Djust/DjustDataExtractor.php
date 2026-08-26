<?php

declare(strict_types=1);

namespace App\Service\Djust;

use App\Enum\Djust\DjustCustomField;
use App\Enum\Djust\DjustDefaults;

class DjustDataExtractor
{
    public function getLocalizedValue(array|string|null $data, string $locale = DjustDefaults::LOCALE->value): string
    {
        if ($data === null) {
            return '';
        }

        if (\is_string($data)) {
            return $data;
        }

        if (isset($data[$locale])) {
            return (string) $data[$locale];
        }

        return !empty($data) ? (string) \reset($data) : '';
    }

    public function findAttributeByExternalId(array $attributeValues, string $externalId): ?array
    {
        foreach ($attributeValues as $attrValue) {
            if (($attrValue['attribute']['externalId'] ?? '') === $externalId) {
                return $attrValue;
            }
        }

        return null;
    }

    public function extractCustomFieldValue(
        array $customFieldValues,
        DjustCustomField $field,
        ?string $expectedType = null,
    ): mixed {
        $expectedExternalId = \strtolower($field->value);
        foreach ($customFieldValues as $cfv) {
            $externalId = \strtolower(
                $cfv['customField']['externalId']
                ?? $cfv['attribute']['externalId']
                ?? ''
            );

            if ($externalId === $expectedExternalId) {
                $type = $cfv['customField']['type'] ?? $cfv['attribute']['type'] ?? '';

                if ($expectedType !== null && $type !== $expectedType) {
                    continue;
                }

                return $this->extractValueFromCustomField($cfv);
            }
        }

        return null;
    }

    public function extractAttachments(array $customFieldValues): array
    {
        $attachments = [];

        foreach ($customFieldValues as $cfv) {
            $fieldType = $cfv['customField']['type'] ?? '';
            $externalId = $cfv['customField']['externalId'] ?? '';

            $field = DjustCustomField::tryFrom($externalId);

            if ($fieldType === 'MEDIA' && $field?->isAttachment()) {
                $fieldName = $this->getLocalizedValue($cfv['customField']['name'] ?? []);
                $url = $this->extractValueFromCustomField($cfv);

                if (\is_string($url) && !empty($url) && \filter_var($url, \FILTER_VALIDATE_URL)) {
                    $attachments[] = [
                        'name' => $fieldName ?: 'Document',
                        'url' => $url,
                        'type' => $this->guessFileType($url),
                    ];
                }
            }
        }

        return $attachments;
    }

    public function extractImages(array $productData): array
    {
        $images = [];
        $imagePaths = [];

        $productPictures = $productData['productPictures'] ?? [];
        \usort($productPictures, static fn (array $a, array $b) => ($b['isMain'] ?? false) <=> ($a['isMain'] ?? false));

        foreach ($productPictures as $picture) {
            $bestUrl = $this->getOptimalSizeImageUrl($picture['urls'] ?? []);
            if ($bestUrl) {
                $imagePath = $this->getImagePathWithoutParams($bestUrl);
                if (!isset($imagePaths[$imagePath])) {
                    $images[] = $bestUrl;
                    $imagePaths[$imagePath] = true;
                }
            }
        }

        foreach ($productData['imageLinks'] ?? [] as $imageUrl) {
            if (!empty($imageUrl)) {
                $imagePath = $this->getImagePathWithoutParams($imageUrl);
                if (!isset($imagePaths[$imagePath])) {
                    $images[] = $imageUrl;
                    $imagePaths[$imagePath] = true;
                }
            }
        }

        return $images;
    }

    public function extractSingleOfferPrice(array $offer): array
    {
        $price = null;
        $priceReference = null;
        $defaultInventory = $offer['offerInventory'] ?? null;
        $variant = $defaultInventory['variant'] ?? [];
        $defaultVariantId = (string) ($variant['id'] ?? $defaultInventory['id'] ?? '');
        $priceRanges = [];

        foreach ($offer['offerPrices'] ?? [] as $offerPrice) {
            foreach ($offerPrice['priceRanges'] ?? [] as $range) {
                $quantity = $range['quantity'] ?? null;
                $basePrice = $range['price']['unitPrice'] ?? $range['price']['itemPrice'] ?? null;
                $discountPrice = $range['discountPrice']['itemPrice'] ?? null;

                if ($quantity !== null && $basePrice !== null) {
                    $rangePrice = $discountPrice !== null ? (float) $discountPrice : (float) $basePrice;
                    $priceRanges[] = [
                        'quantity' => (int) $quantity,
                        'price' => $rangePrice,
                        'priceReference' => (float) $basePrice,
                    ];
                }
            }
        }

        \usort($priceRanges, static fn ($a, $b) => $a['quantity'] <=> $b['quantity']);

        if (!empty($priceRanges)) {
            $defaultRange = $priceRanges[0];
            $price = $defaultRange['price'];
            $priceReference = $defaultRange['priceReference'];
        }

        return [
            'price' => $price,
            'priceReference' => $priceReference,
            'defaultInventory' => $defaultInventory,
            'variantId' => $defaultVariantId,
            'priceRanges' => $priceRanges,
        ];
    }

    public function guessFileType(string $url): string
    {
        $path = \parse_url($url, \PHP_URL_PATH) ?? '';
        $extension = \strtolower(\pathinfo($path, \PATHINFO_EXTENSION));

        return match ($extension) {
            'pdf' => 'pdf',
            'doc', 'docx' => 'word',
            'xls', 'xlsx' => 'excel',
            'ppt', 'pptx' => 'powerpoint',
            'jpg', 'jpeg', 'png', 'gif', 'webp' => 'image',
            'zip', 'rar', '7z' => 'archive',
            default => 'file',
        };
    }

    public function calculateDiscountPercent(?float $priceReference, ?float $price): float
    {
        if (!$priceReference || !$price || $priceReference <= $price) {
            return 0;
        }

        return \round((($priceReference - $price) * 100) / $priceReference);
    }

    public function extractSellerId(array $orderLogistic): int
    {
        $rawSellerId = $orderLogistic['supplierSnapshot']['id'] ?? null;

        if ($rawSellerId === null) {
            return 0;
        }

        if (\is_int($rawSellerId)) {
            return $rawSellerId;
        }

        if (\is_string($rawSellerId) && \preg_match('/\d+/', $rawSellerId, $matches) === 1) {
            return (int) $matches[0];
        }

        return 0;
    }

    private function extractValueFromCustomField(array $cfv): mixed
    {
        $value = $cfv['value'] ?? null;

        if (\is_array($value) && isset($value['value'])) {
            return $value['value'];
        }

        return $value;
    }

    private function getImagePathWithoutParams(string $url): string
    {
        $parsed = \parse_url($url);

        return ($parsed['host'] ?? '').($parsed['path'] ?? '');
    }

    private function getOptimalSizeImageUrl(array $urls): ?string
    {
        if (empty($urls)) {
            return null;
        }

        \usort($urls, static function ($a, $b) {
            $formatA = \strtoupper($a['formatType'] ?? '');
            $formatB = \strtoupper($b['formatType'] ?? '');

            if ($formatA === 'WEBP' && $formatB !== 'WEBP') {
                return -1;
            }
            if ($formatB === 'WEBP' && $formatA !== 'WEBP') {
                return 1;
            }

            $sizeA = ($a['widthInPx'] ?? 0) * ($a['heightInPx'] ?? 0);
            $sizeB = ($b['widthInPx'] ?? 0) * ($b['heightInPx'] ?? 0);

            return $sizeB <=> $sizeA;
        });

        return $urls[0]['url'] ?? null;
    }
}
