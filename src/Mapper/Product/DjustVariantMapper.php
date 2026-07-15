<?php

declare(strict_types=1);

namespace App\Mapper\Product;

use App\Dto\Product;
use App\Dto\Variant;
use App\Enum\Djust\DjustCustomField;
use App\Service\Djust\DjustDataExtractor;
use Psr\Log\LoggerInterface;
use Sentry\State\Scope;

use function Sentry\captureMessage;
use function Sentry\withScope;

class DjustVariantMapper
{
    public function __construct(
        private readonly DjustDataExtractor $extractor,
        private readonly LoggerInterface $djustLogger,
    ) {
    }

    public function mapVariants(Product $product, array $offers, array $singleOffer): void
    {
        $priceData = $this->extractor->extractSingleOfferPrice($singleOffer);
        $variantId = $priceData['variantId'] ?? '';

        if (empty($variantId)) {
            throw new \RuntimeException('Aucun variant ID trouvé dans les données Djust');
        }

        $product->setDefaultVariantId($variantId);

        [$variants, $options] = $this->extractVariantsAndOptions($offers);

        $this->validateVariantsOptionsConsistency($product, $variants, $options);

        $product->setVariants($variants);
        $product->setOptions($options);

        $this->setDefaultVariantPrices($product, $variants);
    }

    private function setDefaultVariantPrices(Product $product, array $variants): void
    {
        $defaultVariantId = $product->getDefaultVariantId();

        foreach ($variants as $variant) {
            if ($variant->getId() === $defaultVariantId) {
                $product->setPrice($variant->getPrice());
                $product->setPriceReference($variant->getPriceReference());
                $product->setPercent($variant->getPercent());
                $product->setPriceRanges($variant->getPriceRanges());
                break;
            }
        }
    }


    private function validateVariantsOptionsConsistency(
        Product $product,
        array $variants,
        array $options
    ): void {
        if (empty($options) || empty($variants)) {
            return;
        }

        $expectedOptionNames = \array_keys($options);
        $incompleteVariants = $this->findIncompleteVariants($variants, $expectedOptionNames);

        if (empty($incompleteVariants)) {
            $this->djustLogger->debug('Product variants validation passed', [
                'product_id' => $product->getId(),
                'variants_count' => \count($variants),
                'options_count' => \count($options),
            ]);
            return;
        }

        $this->djustLogger->warning('Incohérence détectée : variants avec options manquantes', [
            'product_id' => $product->getId(),
            'product_name' => $product->getName(),
            'expected_options' => $expectedOptionNames,
            'incomplete_variants' => $incompleteVariants,
        ]);

        $this->sendSentryAlert($product, $expectedOptionNames, $incompleteVariants);
    }

    private function findIncompleteVariants(array $variants, array $expectedOptionNames): array
    {
        $incompleteVariants = [];

        foreach ($variants as $variant) {
            $variantOptions = $variant->getOptions();
            $definedOptionNames = \array_keys($variantOptions);
            $missingOptions = \array_diff($expectedOptionNames, $definedOptionNames);

            if (!empty($missingOptions)) {
                $incompleteVariants[] = [
                    'variant_id' => $variant->getId(),
                    'variant_external_id' => $variant->getExternalId(),
                    'missing_options' => \array_values($missingOptions),
                    'defined_options' => $definedOptionNames,
                ];
            }
        }

        return $incompleteVariants;
    }

    private function sendSentryAlert(Product $product, array $expectedOptions, array $incompleteVariants): void
    {
        if (!\function_exists('Sentry\captureMessage')) {
            return;
        }

        withScope(function (Scope $scope) use ($product, $expectedOptions, $incompleteVariants): void {
            $scope->setTag('product_id', (string) $product->getId());
            $scope->setTag('issue_type', 'incomplete_variants');
            $scope->setContext('product', [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'expected_options' => $expectedOptions,
                'incomplete_variants' => $incompleteVariants,
            ]);
            captureMessage('Product variants with missing options detected');
        });
    }

    private function extractVariantsAndOptions(array $offers): array
    {
        $variants = [];
        $options = [];
        $optionDefinitions = [];
        $seenVariantIds = [];

        foreach ($offers as $offer) {
            $inventory = $offer['offerInventory'] ?? [];
            $variant = $inventory['variant'] ?? [];

            if (empty($variant['id'])) {
                $this->djustLogger->error('Variant ID missing in offer', [
                    'offerId' => $offer['id'] ?? 'unknown',
                    'inventoryId' => $inventory['id'] ?? 'unknown',
                ]);
                throw new \RuntimeException('Variant ID is required to ensure uniqueness of offers per variant.');
            }

            $variantId = (string) $variant['id'];

            if (in_array($variantId, $seenVariantIds, true)) {
                $this->djustLogger->info('Multiple offers found for the same variant, keeping first offer only', [
                    'variantId' => $variantId,
                    'offerId' => $offer['id'] ?? 'unknown',
                ]);
                continue;
            }

            $seenVariantIds[] = $variantId;
            $variantExternalId = $variant['externalId'] ?? $inventory['externalId'] ?? '';

            $priceData = $this->extractor->extractSingleOfferPrice($offer);

            $percent = $this->extractor->calculateDiscountPercent(
                $priceData['priceReference'],
                $priceData['price']
            );

            $variantImages = $this->extractor->extractImages($variant);

            $offerPriceExternalId = null;
            if (!empty($offer['offerPrices'][0]['externalId'])) {
                $offerPriceExternalId = $offer['offerPrices'][0]['externalId'];
            }

            $variantOptions = [];

            foreach ($variant['attributeValues'] ?? [] as $attrValue) {
                $attrNames = $attrValue['attribute']['name'] ?? [];
                $attrName = $this->extractor->getLocalizedValue($attrNames);
                $externalId = \strtolower($attrValue['attribute']['externalId'] ?? '');
                $attrId = (string) ($attrValue['attribute']['id'] ?? '');
                $rawValue = $attrValue['value'] ?? '';

                if ($attrName === '' || $externalId === \strtolower(DjustCustomField::PRODUCT_ACCORD_ID->value)) {
                    continue;
                }

                $value = $this->normalizeAttributeValue($rawValue);

                $variantOptions[$attrName] = $value;

                if (!isset($optionDefinitions[$attrName])) {
                    $optionDefinitions[$attrName] = [
                        'id' => $attrId,
                        'name' => $attrName,
                        'values' => [],
                    ];
                }

                $valueExists = false;
                foreach ($optionDefinitions[$attrName]['values'] as $existingValue) {
                    if ($this->areValuesEqual($existingValue, $value)) {
                        $valueExists = true;
                        break;
                    }
                }

                if (!$valueExists) {
                    $optionDefinitions[$attrName]['values'][] = $value;
                }
            }

            $variant = new Variant();
            $variant->setId($variantId);
            $variant->setExternalId($variantExternalId);
            $variant->setOfferPriceExternalId($offerPriceExternalId);
            $variant->setOptions($variantOptions);
            $variant->setPrice($priceData['price']);
            $variant->setPriceReference($priceData['priceReference']);
            $variant->setPercent($percent);
            $variant->setImages($variantImages);
            $variant->setPriceRanges($priceData['priceRanges'] ?? []);

            $variants[] = $variant;
        }

        foreach ($optionDefinitions as $optionName => $optionData) {
            $options[$optionName] = [
                'id' => $optionData['id'],
                'name' => $optionName,
                'values' => $optionData['values'],
            ];
        }

        $this->djustLogger->info('Variants and options extracted', [
            'variantsCount' => \count($variants),
            'optionsCount' => \count($options),
            'variantIds' => \array_map(fn(Variant $v) => $v->getId(), $variants),
        ]);

        return [$variants, $options];
    }

    private function normalizeAttributeValue(mixed $value): mixed
    {
        if (!\is_array($value)) {
            return $value;
        }

        if (\array_keys($value) !== \range(0, \count($value) - 1)) {
            return $value;
        }

        return $value[0] ?? $value;
    }

    private function areValuesEqual(mixed $value1, mixed $value2): bool
    {
        if ($value1 === null && $value2 === null) {
            return true;
        }
        if ($value1 === null || $value2 === null) {
            return false;
        }

        if ((\is_array($value1) || \is_object($value1)) && (\is_array($value2) || \is_object($value2))) {
            return \json_encode($value1) === \json_encode($value2);
        }

        return (string) $value1 === (string) $value2;
    }
}
