<?php

declare(strict_types=1);

use App\Dto\Category;
use App\Dto\Seller;
use App\Enum\Storyblok\AccordCadreBlock;
use App\Entity\Account;
use App\Enum\Djust\DjustCustomField;
use App\Factory\CategoryFactory;
use App\Factory\DjustProductFactory;
use App\Factory\SellerFactory;
use App\Mapper\Product\DjustAccordCadreMapper;
use App\Mapper\Product\DjustCategoryMapper;
use App\Mapper\Product\DjustOfferMapper;
use App\Mapper\Product\DjustVariantMapper;
use App\Dto\AccordCadre\ListBlocks\NegociatedTermsBlockContent;
use App\Dto\AccordCadre\ListBlocks\PresentationBlockContent;
use App\Repository\AccordStatutRepository;
use App\Repository\AccountRepository;
use App\Service\AccordCadre\AccordCadreService;
use App\Service\Account\CurrentAccountProvider;
use App\Service\Djust\DjustDataExtractor;
use App\Service\Djust\Product\DjustProductTypeExtractor;
use App\Service\Djust\Product\DjustPropertyFilter;
use App\Service\Djust\DjustSellerService;
use App\Service\Product\ProductDescriptionFormatter;
use App\Service\Shipping\ShippingCostResolver;
use Psr\Log\LoggerInterface;

uses()->group('UnitDjustProductFactory');

// Les logs (djustLogger) ne sont pas testés.

\beforeEach(function () {
    $this->extractor = new DjustDataExtractor();
    $this->productTypeExtractor = new DjustProductTypeExtractor($this->extractor);
    $this->accountRepository = Mockery::mock(AccountRepository::class);
    $this->accordStatutRepository = Mockery::mock(AccordStatutRepository::class);

    // Mock CategoryFactory
    $this->categoryFactory = Mockery::mock(CategoryFactory::class);
    $this->categoryFactory->shouldReceive('createAndAddToCollection')
        ->andReturnUsing(function ($data) {
            if (empty($data)) {
                return [];
            }

            $categories = [];
            foreach ($data as $categoryData) {
                $category = new Category();
                $category->setId((string) ($categoryData['id'] ?? 'test-id'));
                $category->setName($categoryData['name'] ?? 'Test Category');
                $categories[] = $category;
            }

            return $categories;
        });

    // Mock du logger
    /** @var LoggerInterface */
    $this->logger = Mockery::mock(LoggerInterface::class)->shouldIgnoreMissing();

    // Création des mappers
    $this->variantMapper = new DjustVariantMapper($this->extractor, $this->logger);
    $this->categoryMapper = new DjustCategoryMapper($this->categoryFactory);
    $this->propertyFilter = new DjustPropertyFilter($this->extractor);
    $this->accordCadreMapper = new DjustAccordCadreMapper(
        $this->accountRepository,
        $this->accordStatutRepository,
    );
    $this->offerMapper = new DjustOfferMapper(
        $this->extractor,
    );
    $this->accordCadreService = Mockery::mock(AccordCadreService::class);
    $this->accordCadreService->shouldReceive('getAccordCadreContentByTarifId')->andReturn(null);

    $this->currentAccountProvider = Mockery::mock(CurrentAccountProvider::class);
    $mockAccount = Mockery::mock(Account::class);
    $mockAccount->shouldReceive('getDjustCustomerAccountId')->andReturn('customer-account-id');
    $this->currentAccountProvider->shouldReceive('getRequiredAccount')->andReturn($mockAccount);

    $this->shippingCostResolver = Mockery::mock(ShippingCostResolver::class);
    $this->shippingCostResolver->shouldReceive('resolveByCategory')->andReturn(null);

    $this->djustSellerService = Mockery::mock(DjustSellerService::class);
    $this->djustSellerService->shouldReceive('getSeller')->andReturn(null);

    $this->sellerFactory = Mockery::mock(SellerFactory::class);
    $this->sellerFactory->shouldReceive('create')
        ->andReturnUsing(function (array $data) {
            $extractSupplierFieldByExternalId = function (array $customFieldValues, string $externalId, bool $exact = true) {
                $expectedExternalId = \strtolower($externalId);
                $results = $exact ? null : [];

                foreach ($customFieldValues as $cfv) {
                    $fieldExternalId = \strtolower($cfv['customField']['externalId'] ?? '');

                    if ($exact && $fieldExternalId === $expectedExternalId) {
                        $valueWrapper = $cfv['value'] ?? null;

                        return \is_array($valueWrapper) && isset($valueWrapper['value']) ? $valueWrapper['value'] : $valueWrapper;
                    } elseif (\str_starts_with($fieldExternalId, $expectedExternalId)) {
                        $valueWrapper = $cfv['value'] ?? null;
                        $results[$fieldExternalId] = \is_array($valueWrapper) && isset($valueWrapper['value']) ? $valueWrapper['value'] : $valueWrapper;
                    }
                }

                return $results;
            };

            $seller = new Seller();
            $seller->setId($data['id'] ?? null);
            $seller->setName($data['name'] ?? '');
            $seller->setDescription($data['description'] ?? '');
            $seller->setAvatar($data['avatar'] ?? null);

            return $seller;
        });

    $this->factory = new DjustProductFactory(
        $this->accordCadreService,
        $this->extractor,
        $this->productTypeExtractor,
        $this->variantMapper,
        $this->categoryMapper,
        $this->propertyFilter,
        $this->accordCadreMapper,
        $this->offerMapper,
        $this->currentAccountProvider,
        $this->sellerFactory,
        $this->shippingCostResolver,
        $this->logger,
        new ProductDescriptionFormatter(),
        $this->djustSellerService,
    );

    $this->product = \json_decode(\file_get_contents(__DIR__.'/../../Api/_data/_mocks/djust-response/products/product.json'), true);
    $offersResponse = \json_decode(\file_get_contents(__DIR__.'/../../Api/_data/_mocks/djust-response/products/offers.json'), true);
    $this->offers = $offersResponse['content'] ?? [];
});

\it('creates product with correct properties', function () {
    $result = $this->factory->create(['product' => $this->product, 'offers' => $this->offers]);

    \expect($result)->toBeInstanceOf(App\Dto\Product::class);
    \expect($result->getId())->toBe('0000001138');
    \expect($result->getName())->not()->toBeEmpty();
    \expect($result->getImages())->toBeArray();
});

\it('sets externalId from product data', function () {
    $result = $this->factory->create(['product' => $this->product, 'offers' => $this->offers]);

    \expect($result->getExternalId())->toBe('cahier_ext');
});

\it('sets slug to externalId when externalId is present', function () {
    $result = $this->factory->create(['product' => $this->product, 'offers' => $this->offers]);

    \expect($result->getSlug())->toBe('cahier_ext');
});

\it('sets slug to null when externalId is null', function () {
    $productWithoutExternalId = $this->product;
    unset($productWithoutExternalId['externalId']);

    $result = $this->factory->create(['product' => $productWithoutExternalId, 'offers' => $this->offers]);

    \expect($result->getExternalId())->toBeNull();
    \expect($result->getSlug())->toBeNull();
});

\it('sets seller from offers', function () {
    $result = $this->factory->create(['product' => $this->product, 'offers' => $this->offers]);

    \expect($result->getSeller())->not()->toBeNull();
    \expect($result->getSeller()->getName())->not()->toBeEmpty();
});

\it('maps properties with brand and partner name', function () {
    $result = $this->factory->create(['product' => $this->product, 'offers' => $this->offers]);

    $properties = $result->getProperties();
    \expect($properties)->toBeArray();
});

\it('extracts attachments from offers', function () {
    $result = $this->factory->create(['product' => $this->product, 'offers' => $this->offers]);

    $attachments = $result->getAttachments();
    \expect($attachments)->toBeArray();
    \expect($attachments)->toHaveCount(1);
    \expect($attachments[0])->toHaveKeys(['name', 'url', 'type']);
    \expect($attachments[0]['name'])->toBe('Conditions négociées');
    \expect($attachments[0]['url'])->toContain('1766498681129.pdf');
});

\it('sets price data from offers', function () {
    $result = $this->factory->create(['product' => $this->product, 'offers' => $this->offers]);

    \expect($result->getPrice())->toBeGreaterThan(0);
    \expect($result->getPriceReference())->toBeGreaterThan(0);
});

\it('sets quantity limits', function () {
    $result = $this->factory->create(['product' => $this->product, 'offers' => $this->offers]);

    \expect($result->getMinOrderQuantity())->toBeGreaterThanOrEqual(1);
    \expect($result->getMaxOrderQuantity())->toBeGreaterThan(0);
});

\it('maps categories from product', function () {
    $result = $this->factory->create(['product' => $this->product, 'offers' => $this->offers]);

    $categories = $result->getCategories();
    \expect($categories)->toBeArray();
    \expect($categories)->not()->toBeEmpty();
    \expect($categories[0])->toBeInstanceOf(Category::class);
});

\it('maps properties from product attributes', function () {
    $result = $this->factory->create(['product' => $this->product, 'offers' => $this->offers]);

    $properties = $result->getProperties();
    \expect($properties)->toBeArray();
    \expect($properties)->toHaveKey('Marque');
    \expect($properties['Marque'])->toBe('Oxford');
});

\it('maps whitelisted tags from product tags', function () {
    $result = $this->factory->create(['product' => $this->product, 'offers' => $this->offers]);

    $tags = $result->getTags();
    \expect($tags)->toBeArray();
    \expect($tags)->toContain('made_in_france');
    \expect($tags)->toContain('achat_durable');
});

\it('does not include tags in properties', function () {
    $result = $this->factory->create(['product' => $this->product, 'offers' => $this->offers]);

    $properties = $result->getProperties();
    \expect($properties)->not()->toHaveKey('tag_made_in_france');
    \expect($properties)->not()->toHaveKey('tag_achat_durable');
});

\it('extracts product type from attributes', function () {
    $result = $this->factory->create(['product' => $this->product, 'offers' => $this->offers]);

    \expect($result->getProductType())->not()->toBeNull();
    \expect($result->getProductType())->toBeIn(['SELLABLE', 'NOT_SELLABLE', 'ACCORD_CADRE']);
});

\it('extracts not sellable custom fields for NOT_SELLABLE products', function () {
    $result = $this->factory->create(['product' => $this->product, 'offers' => $this->offers]);

    \expect($result->getProductType())->toBe('NOT_SELLABLE');

    \expect($result->getProductTopLabel())->toBe('Offre exclusive');
    \expect($result->getProductPricingPhrase())->toBe('À partir de x euros');
});

\it('sets notSellableFormWithMessage to true when PRODUCT_FORM_WITH_MESSAGE is Oui', function () {
    $result = $this->factory->create(['product' => $this->product, 'offers' => $this->offers]);

    \expect($result->isNotSellableFormWithMessage())->toBeTrue();
});

\it('sets notSellableFormWithMessage to false when PRODUCT_FORM_WITH_MESSAGE is absent', function () {
    // Modifier le produit pour retirer l'attribut
    $productWithoutForm = $this->product;
    $productWithoutForm['attributeValues'] = \array_filter(
        $productWithoutForm['attributeValues'],
        fn ($attr) => ($attr['attribute']['externalId'] ?? '') !== DjustCustomField::PRODUCT_FORM_WITH_MESSAGE->value
    );

    $result = $this->factory->create(['product' => $productWithoutForm, 'offers' => $this->offers]);

    \expect($result->isNotSellableFormWithMessage())->toBeFalse();
});

\it('extracts tarifId at product level from offers', function () {
    $result = $this->factory->create(['product' => $this->product, 'offers' => $this->offers]);

    \expect($result->getTarifId())->not()->toBeNull();
    \expect($result->getTarifId())->toBeString();
});

\it('returns product with cleared accord cadre data when ACCORD_CADRE has no tarifId', function () {
    $accordCadreProduct = $this->product;
    $accordCadreProduct['attributeValues'] = \array_filter(
        $accordCadreProduct['attributeValues'],
        fn ($attr) => ($attr['attribute']['externalId'] ?? '') !== DjustCustomField::PRODUCT_TYPE->value
    );
    $accordCadreProduct['attributeValues'][] = [
        'attribute' => ['externalId' => DjustCustomField::PRODUCT_TYPE->value],
        'value' => ['ACCORD_CADRE'],
    ];

    $offersWithoutTarifId = $this->offers;
    foreach ($offersWithoutTarifId as &$offer) {
        if (isset($offer['offerInventory']['customFieldValues'])) {
            $offer['offerInventory']['customFieldValues'] = \array_filter(
                $offer['offerInventory']['customFieldValues'],
                fn ($cfv) => ($cfv['customField']['externalId'] ?? '') !== DjustCustomField::OFFER_TARIF_ID->value
            );
        }
    }

    $result = $this->factory->create(['product' => $accordCadreProduct, 'offers' => $offersWithoutTarifId]);

    \expect($result->getAccordCadreContent())->toBeNull();
    \expect($result->getTarifId())->toBeNull();
    \expect($result->getAccordId())->toBeNull();
});

\it('returns product with cleared accord cadre data when ACCORD_CADRE has tarifId but no Storyblok content', function () {
    $accordCadreProduct = $this->product;
    $accordCadreProduct['attributeValues'] = \array_filter(
        $accordCadreProduct['attributeValues'],
        fn ($attr) => ($attr['attribute']['externalId'] ?? '') !== DjustCustomField::PRODUCT_TYPE->value
    );
    $accordCadreProduct['attributeValues'][] = [
        'attribute' => ['externalId' => DjustCustomField::PRODUCT_TYPE->value],
        'value' => ['ACCORD_CADRE'],
    ];

    $result = $this->factory->create(['product' => $accordCadreProduct, 'offers' => $this->offers]);

    \expect($result->getAccordCadreContent())->toBeNull();
    \expect($result->getTarifId())->toBeNull();
    \expect($result->getAccordId())->toBeNull();
});

\it('sets accordCadreContent when ACCORD_CADRE has tarifId and content', function () {
    $presentationBlock = new PresentationBlockContent();
    $presentationBlock->setComponentName(AccordCadreBlock::PRESENTATION->value);

    $negociatedTermsBlock = new NegociatedTermsBlockContent();
    $negociatedTermsBlock->setComponentName(AccordCadreBlock::NEGOCIATED_TERMS->value);

    $accordCadreContent = new App\Dto\AccordCadre\AccordCadreContent();
    $accordCadreContent->setTarifId('test-tarif-multi-ranges');
    $accordCadreContent->addListBlock($presentationBlock);
    $accordCadreContent->addListBlock($negociatedTermsBlock);

    // Mock AccordCadreService qui retourne du contenu
    $accordCadreService = Mockery::mock(AccordCadreService::class);
    $accordCadreService->shouldReceive('getAccordCadreContentByTarifId')
        ->with('test-tarif-multi-ranges')
        ->andReturn($accordCadreContent);

    // Recréer la factory avec le nouveau mock
    $factory = new DjustProductFactory(
        $accordCadreService,
        $this->extractor,
        $this->productTypeExtractor,
        $this->variantMapper,
        $this->categoryMapper,
        $this->propertyFilter,
        $this->accordCadreMapper,
        $this->offerMapper,
        $this->currentAccountProvider,
        $this->sellerFactory,
        $this->shippingCostResolver,
        $this->logger,
        new ProductDescriptionFormatter(),
        $this->djustSellerService,
    );

    // Créer un produit de type ACCORD_CADRE
    $accordCadreProduct = $this->product;
    $accordCadreProduct['attributeValues'] = \array_filter(
        $accordCadreProduct['attributeValues'],
        fn ($attr) => ($attr['attribute']['externalId'] ?? '') !== DjustCustomField::PRODUCT_TYPE->value
    );
    $accordCadreProduct['attributeValues'][] = [
        'attribute' => ['externalId' => DjustCustomField::PRODUCT_TYPE->value],
        'value' => ['ACCORD_CADRE'],
    ];

    // Les offres contiennent désormais le tarifId 'test-tarif-multi-ranges' dans le fichier mock
    $result = $factory->create(['product' => $accordCadreProduct, 'offers' => $this->offers]);

    \expect($result->getAccordCadreContent())->toBe($accordCadreContent);
    \expect($result->getTarifId())->toBe('test-tarif-multi-ranges');
});

\it('formats description with line breaks after period and space before capital letter', function () {
    $masterProduct = [
        'id' => '123',
        'description' => ['fr' => 'Première phrase. Deuxième phrase.'],
        'images' => [],
    ];

    $result = $this->factory->create(['product' => $masterProduct, 'offers' => $this->offers]);

    \expect($result->getDescription())->toContain('<br><br>');
});

\it('formats description with line breaks when lowercase followed by uppercase', function () {
    $masterProduct = [
        'id' => '123',
        'description' => ['fr' => 'Format A4Livraison gratuite'],
        'images' => [],
    ];

    $result = $this->factory->create(['product' => $masterProduct, 'offers' => $this->offers]);

    \expect($result->getDescription())->toContain('A4<br><br>Livraison');
});

\it('decodes HTML entities in description', function () {
    $masterProduct = [
        'id' => '123',
        'description' => ['fr' => 'Chemise &agrave; rabats&nbsp;Format A4'],
        'images' => [],
    ];

    $result = $this->factory->create(['product' => $masterProduct, 'offers' => $this->offers]);

    \expect($result->getDescription())->toContain('à rabats');
    \expect($result->getDescription())->toContain(' Format'); // nbsp converti en espace
});

\it('formats description with line break after period without space before capital', function () {
    $masterProduct = [
        'id' => '123',
        'description' => ['fr' => 'Première phrase.Deuxième phrase'],
        'images' => [],
    ];

    $result = $this->factory->create(['product' => $masterProduct, 'offers' => $this->offers]);

    \expect($result->getDescription())->toContain('.<br><br>Deuxième');
});

\it('injects fdp_ht into supplierDeliveryInfo when category matches', function () {
    $shippingCostResolver = Mockery::mock(ShippingCostResolver::class);
    $shippingCostResolver->shouldReceive('resolveByCategory')
        ->with(Mockery::any(), 'COMEBACK18')
        ->andReturn(18.0);

    $sellerFactory = Mockery::mock(SellerFactory::class);
    $sellerFactory->shouldReceive('create')->andReturnUsing(function () {
        $seller = new Seller();
        $seller->setId('supplier-uuid');
        $seller->setExternalId('partner-uuid');
        $seller->setName('Test Supplier');
        $seller->setSupplierDeliveryInfo('%s€ HT de frais de port pour une commande inférieure à 1700€ HT.');

        return $seller;
    });

    $factory = new DjustProductFactory(
        $this->accordCadreService,
        $this->extractor,
        $this->productTypeExtractor,
        $this->variantMapper,
        $this->categoryMapper,
        $this->propertyFilter,
        $this->accordCadreMapper,
        $this->offerMapper,
        $this->currentAccountProvider,
        $sellerFactory,
        $shippingCostResolver,
        $this->logger,
        new ProductDescriptionFormatter(),
        $this->djustSellerService,
    );

    $product = $this->product;
    $product['attributeValues'][] = [
        'attribute' => ['externalId' => DjustCustomField::PRODUCT_SHIPPING_CATEGORY->value],
        'value' => ['COMEBACK18'],
    ];

    $result = $factory->create(['product' => $product, 'offers' => $this->offers]);

    \expect($result->getShippingCost())->toBe(18.0);
    \expect($result->getSeller()->getSupplierDeliveryInfo())->toBe('18€ HT de frais de port pour une commande inférieure à 1700€ HT.');
});

\it('sets supplierDeliveryInfo to null when category not found in rules', function () {
    $this->shippingCostResolver->shouldReceive('resolveByCategory')
        ->andReturn(null);

    $sellerFactory = Mockery::mock(SellerFactory::class);
    $sellerFactory->shouldReceive('create')->andReturnUsing(function () {
        $seller = new Seller();
        $seller->setId('supplier-uuid');
        $seller->setExternalId('partner-uuid');
        $seller->setName('Test Supplier');
        $seller->setSupplierDeliveryInfo('%s€ HT de frais de port pour une commande inférieure à 1700€ HT.');

        return $seller;
    });

    $factory = new DjustProductFactory(
        $this->accordCadreService,
        $this->extractor,
        $this->productTypeExtractor,
        $this->variantMapper,
        $this->categoryMapper,
        $this->propertyFilter,
        $this->accordCadreMapper,
        $this->offerMapper,
        $this->currentAccountProvider,
        $sellerFactory,
        $this->shippingCostResolver,
        $this->logger,
        new ProductDescriptionFormatter(),
        $this->djustSellerService,
    );

    $product = $this->product;
    $product['attributeValues'][] = [
        'attribute' => ['externalId' => DjustCustomField::PRODUCT_SHIPPING_CATEGORY->value],
        'value' => ['UNKNOWN_CATEGORY'],
    ];

    $result = $factory->create(['product' => $product, 'offers' => $this->offers]);

    \expect($result->getSeller()->getSupplierDeliveryInfo())->toBeNull();
});

\it('sets supplierDeliveryInfo to null when no shipping category attribute on product', function () {
    $sellerFactory = Mockery::mock(SellerFactory::class);
    $sellerFactory->shouldReceive('create')->andReturnUsing(function () {
        $seller = new Seller();
        $seller->setId('supplier-uuid');
        $seller->setExternalId('partner-uuid');
        $seller->setName('Test Supplier');
        $seller->setSupplierDeliveryInfo('%s€ HT de frais de port pour une commande inférieure à 1700€ HT.');

        return $seller;
    });

    $factory = new DjustProductFactory(
        $this->accordCadreService,
        $this->extractor,
        $this->productTypeExtractor,
        $this->variantMapper,
        $this->categoryMapper,
        $this->propertyFilter,
        $this->accordCadreMapper,
        $this->offerMapper,
        $this->currentAccountProvider,
        $sellerFactory,
        $this->shippingCostResolver,
        $this->logger,
        new ProductDescriptionFormatter(),
        $this->djustSellerService,
    );

    $product = $this->product;
    $product['attributeValues'] = \array_filter(
        $product['attributeValues'],
        fn ($attr) => ($attr['attribute']['externalId'] ?? '') !== DjustCustomField::PRODUCT_SHIPPING_CATEGORY->value
    );

    $result = $factory->create(['product' => $product, 'offers' => $this->offers]);

    \expect($result->getSeller()->getSupplierDeliveryInfo())->toBeNull();
});

\it('normalizes initial quantity to minOrderQuantity when lower', function () {
    $offersWithMinQty = $this->offers;
    $offersWithMinQty[0]['offerInventory']['minOrderQuantity'] = 5;
    $offersWithMinQty[0]['offerInventory']['maxOrderQuantity'] = 100;

    $product = $this->product;
    unset($product['quantity']);

    $result = $this->factory->create(['product' => $product, 'offers' => $offersWithMinQty]);

    \expect($result->getMinOrderQuantity())->toBe(5);
    \expect($result->getQuantity())->toBe(5);
});

\it('keeps quantity when already above minOrderQuantity', function () {
    $offersWithMinQty = $this->offers;
    $offersWithMinQty[0]['offerInventory']['minOrderQuantity'] = 1;
    $offersWithMinQty[0]['offerInventory']['maxOrderQuantity'] = 100;

    $product = $this->product;
    $product['quantity'] = 3;

    $result = $this->factory->create(['product' => $product, 'offers' => $offersWithMinQty]);

    \expect($result->getQuantity())->toBe(3);
});

\it('skips defective products in createAndAddToCollection without throwing', function () {
    $validProduct = [
        'product' => $this->product,
        'offers' => $this->offers,
    ];

    $defectiveProduct = [
        'product' => $this->product,
        'offers' => [], // Missing offers => will cause RuntimeException in mapVariants
    ];

    $products = $this->factory->createAndAddToCollection([$validProduct, $defectiveProduct]);

    \expect($products)->toHaveCount(1)
        ->and($products[0]->getName())->not()->toBeEmpty();
});

\it('uses enriched seller data from getSeller when available', function () {
    $supplierIdFromOffer = $this->offers[0]['supplier']['id'] ?? 'supplier-123';

    $enrichedSellerData = [
        'id' => $supplierIdFromOffer,
        'name' => 'Enriched Seller Name',
        'description' => 'Full description from API',
    ];

    $djustSellerService = Mockery::mock(DjustSellerService::class);
    $djustSellerService->shouldReceive('getSeller')
        ->with($supplierIdFromOffer, 'customer-account-id')
        ->once()
        ->andReturn($enrichedSellerData);

    $capturedData = null;
    $sellerFactory = Mockery::mock(SellerFactory::class);
    $sellerFactory->shouldReceive('create')
        ->once()
        ->andReturnUsing(function (array $data) use (&$capturedData) {
            $capturedData = $data;
            $seller = new Seller();
            $seller->setName($data['name'] ?? '');

            return $seller;
        });

    $factory = new DjustProductFactory(
        $this->accordCadreService,
        $this->extractor,
        $this->productTypeExtractor,
        $this->variantMapper,
        $this->categoryMapper,
        $this->propertyFilter,
        $this->accordCadreMapper,
        $this->offerMapper,
        $this->currentAccountProvider,
        $sellerFactory,
        $this->shippingCostResolver,
        $this->logger,
        new ProductDescriptionFormatter(),
        $djustSellerService,
    );

    $factory->create(['product' => $this->product, 'offers' => $this->offers]);

    \expect($capturedData)->toBe($enrichedSellerData);
});

\it('falls back to offer supplier data when getSeller returns null', function () {
    $supplierFromOffer = $this->offers[0]['supplier'] ?? [];

    $djustSellerService = Mockery::mock(DjustSellerService::class);
    $djustSellerService->shouldReceive('getSeller')->andReturn(null);

    $capturedData = null;
    $sellerFactory = Mockery::mock(SellerFactory::class);
    $sellerFactory->shouldReceive('create')
        ->once()
        ->andReturnUsing(function (array $data) use (&$capturedData) {
            $capturedData = $data;
            $seller = new Seller();

            return $seller;
        });

    $factory = new DjustProductFactory(
        $this->accordCadreService,
        $this->extractor,
        $this->productTypeExtractor,
        $this->variantMapper,
        $this->categoryMapper,
        $this->propertyFilter,
        $this->accordCadreMapper,
        $this->offerMapper,
        $this->currentAccountProvider,
        $sellerFactory,
        $this->shippingCostResolver,
        $this->logger,
        new ProductDescriptionFormatter(),
        $djustSellerService,
    );

    $factory->create(['product' => $this->product, 'offers' => $this->offers]);

    \expect($capturedData)->toBe($supplierFromOffer);
});

