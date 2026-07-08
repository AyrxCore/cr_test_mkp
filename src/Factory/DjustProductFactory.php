<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\AccordCadre\AccordCadreContent;
use App\Dto\Product;
use App\Entity\Account;
use App\Enum\Djust\DjustCustomField;
use App\Enum\Djust\DjustProductType;
use App\Enum\Storyblok\AccordCadreBlock;
use App\Mapper\Product\DjustAccordCadreMapper;
use App\Mapper\Product\DjustCategoryMapper;
use App\Mapper\Product\DjustOfferMapper;
use App\Mapper\Product\DjustVariantMapper;
use App\Service\AccordCadre\AccordCadreService;
use App\Service\Djust\DjustDataExtractor;
use App\Service\Djust\Product\DjustProductTypeExtractor;
use App\Service\Djust\Product\DjustPropertyFilter;
use App\Service\Account\CurrentAccountProvider;
use App\Service\Product\ProductDescriptionFormatter;
use App\Service\Shipping\ShippingCostResolver;
use App\Service\Djust\DjustSellerService;
use Psr\Log\LoggerInterface;

class DjustProductFactory extends AbstractFactory
{
    public function __construct(
        private readonly AccordCadreService $accordCadreService,
        private readonly DjustDataExtractor $extractor,
        private readonly DjustProductTypeExtractor $productTypeExtractor,
        private readonly DjustVariantMapper $variantMapper,
        private readonly DjustCategoryMapper $categoryMapper,
        private readonly DjustPropertyFilter $propertyFilter,
        private readonly DjustAccordCadreMapper $accordCadreMapper,
        private readonly DjustOfferMapper $offerMapper,
        private readonly CurrentAccountProvider $currentAccountProvider,
        private readonly SellerFactory $sellerFactory,
        private readonly ShippingCostResolver $shippingCostResolver,
        private readonly LoggerInterface $djustLogger,
        private readonly ProductDescriptionFormatter $descriptionFormatter,
        private readonly DjustSellerService $djustSellerService,
    ) {
    }

    public function create(array $baseProduct): Product
    {
        $account = $this->currentAccountProvider->getRequiredAccount();

        $masterProduct = $baseProduct['product'];
        $offers = $baseProduct['offers'];

        $singleOffer = $offers[0] ?? [];
        $defaultInventory = $singleOffer['offerInventory'] ?? null;
        $variant = $defaultInventory['variant'] ?? [];
        $variantExternalId = $variant['externalId'] ?? $defaultInventory['externalId'] ?? null;

        $description = $this->extractor->getLocalizedValue($masterProduct['description'] ?? $masterProduct['info']['descriptions'] ?? []);
        $description = $this->descriptionFormatter->format($description) ?? '';

        $product = $this->initBaseProduct(
            id: $masterProduct['id'],
            name: $this->extractor->getLocalizedValue($masterProduct['name'] ?? $masterProduct['productName'] ?? $masterProduct['info']['names'] ?? []),
            description: $description,
            sku: $masterProduct['sku'] ?? null,
            variantExternalId: $variantExternalId,
            images: $this->extractor->extractImages($masterProduct),
            externalId: $masterProduct['externalId'] ?? null,
        );

        $product->setQuantity($masterProduct['quantity'] ?? 1);
        $supplierData = $offers[0]['supplier'] ?? [];
        $fullSellerData = $this->djustSellerService->getSeller($supplierData['id'] ?? '', $account->getDjustCustomerAccountId()) ?? $supplierData;
        $product->setSeller($this->sellerFactory->create($fullSellerData));
        $productType = $this->productTypeExtractor->extract($masterProduct);
        $product->setProductType($productType->value);

        $this->propertyFilter->mapProperties($product, $masterProduct);
        $this->propertyFilter->mapTags($product, $masterProduct);
        $this->categoryMapper->mapCategories($product, $masterProduct);

        $this->variantMapper->mapVariants($product, $offers, $singleOffer);
        $this->offerMapper->mapOffersData($product, $offers, $productType);

        $product->setQuantity(\max($product->getQuantity() ?? 1, $product->getMinOrderQuantity()));

        $shouldShowForm = $this->propertyFilter->shouldShowFormWithMessage($masterProduct);
        $product->setNotSellableFormWithMessage($shouldShowForm);

        $tarifId = $this->extractTarifIdFromOffers($offers);
        $product->setTarifId($tarifId);

        $shippingAttr = $this->extractor->findAttributeByExternalId(
            $masterProduct['attributeValues'] ?? [],
            DjustCustomField::PRODUCT_SHIPPING_CATEGORY->value
        );
        $shippingCategoryValue = $this->resolveShippingCategoryValue($shippingAttr);
        $product->setShippingCategory($shippingCategoryValue);
        $this->mapShippingCategory($product, $shippingCategoryValue);

        $weightRaw = $this->extractor->extractCustomFieldValue(
            $masterProduct['attributeValues'] ?? [],
            DjustCustomField::PRODUCT_WEIGHT,
        );
        $product->setWeight($weightRaw !== null ? (float) $weightRaw : null);

        $ecoTaxRaw = $this->extractor->extractCustomFieldValue(
            $masterProduct['attributeValues'] ?? [],
            DjustCustomField::PRODUCT_ECOTAXE,
        );
        $product->setEcoTax($ecoTaxRaw !== null ? (float) $ecoTaxRaw : null);

        if ($productType === DjustProductType::ACCORD_CADRE) {
            return $this->processAccordCadreProduct($product, $masterProduct, $productType, $tarifId, $account);
        }

        $this->accordCadreMapper->mapAccordCadre($product, $masterProduct, $productType, $account);

        return $product;
    }

    private function initBaseProduct(
        string $id,
        string $name,
        string $description,
        ?string $sku,
        ?string $variantExternalId,
        array $images,
        ?string $externalId = null,
    ): Product {
        $product = new Product();

        $product->setId($id);
        $product->setName($name);
        $product->setDescription($description);
        $product->setSlug($externalId);
        $product->setReference($variantExternalId);
        $product->setImages($images);
        $product->setSku($sku);
        $product->setExternalId($externalId);

        return $product;
    }

    private function resolveShippingCategoryValue(?array $attrValue): ?string
    {
        if ($attrValue === null) {
            return null;
        }

        $raw = $attrValue['value'] ?? null;

        if ($raw === null) {
            return null;
        }

        if (\is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = \is_array($decoded) ? $decoded : $raw;
        }

        if (\is_array($raw)) {
            $value = !empty($raw) ? (string) ($raw[0] ?? '') : '';
            return $value !== '' ? $value : null;
        }

        $value = (string) $raw;

        return $value !== '' ? $value : null;
    }

    private function mapShippingCategory(Product $product, ?string $categoryValue): void
    {
        $seller = $product->getSeller();
        if ($seller === null) {
            return;
        }

        $template = $seller->getSupplierDeliveryInfo();
        if ($template === null || !\str_contains($template, '%s')) {
            return;
        }

        if ($categoryValue === null) {
            $seller->setSupplierDeliveryInfo(null);

            return;
        }

        $shippingCost = $this->shippingCostResolver->resolveByCategory(
            (string) $seller->getExternalId(),
            $categoryValue
        );

        if ($shippingCost === null) {
            $seller->setSupplierDeliveryInfo(null);

            return;
        }

        $product->setShippingCost($shippingCost);
        $seller->setSupplierDeliveryInfo(\sprintf($template, $shippingCost));
    }

    private function processAccordCadreProduct(
        Product $product,
        array $masterProduct,
        DjustProductType $productType,
        ?string $tarifId,
        Account $account,
    ): Product {
        $product->setAccordId($this->extractAccordId($masterProduct));

        if ($tarifId === null) {
            $this->djustLogger->warning('TarifId manquant pour un produit accord-cadre, accord-cadre ignoré.', [
                'productId' => $product->getId(),
            ]);
            $this->clearAccordCadreData($product);

            return $product;
        }

        $accordCadreContent = $this->accordCadreService->getAccordCadreContentByTarifId($tarifId);
        if (!$this->hasRequiredAccordCadreBlocks($accordCadreContent)) {
            $this->djustLogger->warning('Contenu Storyblok introuvable pour un produit accord-cadre, accord-cadre ignoré.', [
                'productId' => $product->getId(),
                'tarifId' => $tarifId,
            ]);
            $this->clearAccordCadreData($product);

            return $product;
        }

        $this->accordCadreMapper->mapAccordCadre($product, $masterProduct, $productType, $account);
        $product->setAccordCadreContent($accordCadreContent);

        return $product;
    }

    private function extractAccordId(array $masterProduct): ?string
    {
        $attrValue = $this->extractor->findAttributeByExternalId(
            $masterProduct['attributeValues'] ?? [],
            DjustCustomField::PRODUCT_ACCORD_ID->value
        );

        if ($attrValue === null) {
            return null;
        }

        $value = $attrValue['value'] ?? null;

        if (\is_array($value)) {
            if (empty($value)) {
                return null;
            }
            $value = \reset($value);
        }

        return $value !== null && $value !== '' ? (string) $value : null;
    }

    private function extractTarifIdFromOffers(array $offers): ?string
    {
        if (empty($offers)) {
            return null;
        }

        $firstOffer = $offers[0];
        $inventory = $firstOffer['offerInventory'] ?? [];
        $customFieldValues = $inventory['customFieldValues'] ?? [];

        foreach ($customFieldValues as $cfv) {
            $externalId = $cfv['customField']['externalId'] ?? '';

            if ($externalId === DjustCustomField::OFFER_TARIF_ID->value) {
                $value = $cfv['value']['value'] ?? $cfv['value'] ?? null;

                return $value !== null ? (string) $value : null;
            }
        }

        return null;
    }

    private function hasRequiredAccordCadreBlocks(?AccordCadreContent $accordCadreContent): bool
    {
        if ($accordCadreContent === null) {
            return false;
        }

        $listBlocks = $accordCadreContent->getListBlocks();

        return isset(
            $listBlocks[AccordCadreBlock::PRESENTATION->value],
            $listBlocks[AccordCadreBlock::NEGOCIATED_TERMS->value]
        );
    }

    private function clearAccordCadreData(Product $product): void
    {
        $product->setAccordId(null);
        $product->setTarifId(null);
        $product->setAccordCadreContent(null);
    }

    public function createAndAddToCollection(array $data): array
    {
        $array = [];
        foreach ($data as $remoteData) {
            try {
                $array[] = $this->create($remoteData);
            } catch (\Throwable $e) {
                $productId = $remoteData['product']['externalId'] ?? $remoteData['product']['id'] ?? 'unknown';
                $this->djustLogger->error('Failed to create product from Djust data, skipping.', [
                    'productId' => $productId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $array;
    }
}
