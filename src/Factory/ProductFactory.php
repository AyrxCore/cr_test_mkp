<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\AccountAccordCadre;
use App\Dto\Product;
use App\Dto\Property;
use App\Entity\AccordStatut;
use App\Entity\Account;
use App\Entity\Favorite;
use App\Helper\UpplerHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Uid\Uuid;

class ProductFactory extends AbstractFactory
{
    public function __construct(
        protected AdapterInterface $cache,
        private RequestStack $requestStack,
        private EntityManagerInterface $em,
        private CategoryFactory $categoryFactory,
        private SellerFactory $sellerFactory,
    ) {
        parent::__construct($this->cache);
    }

    public function createAndAddToCollection(array $data): array
    {
        $products = [];
        $this->cache->clear();
        foreach ($data as $remoteProduct) {
            $productCached = $this->cache->getItem('collection_product_'.$remoteProduct['id']);
            if (!$productCached->isHit()) {
                $session = $this->requestStack->getSession();
                /** @var Account $account */
                $account = $session->get('account');

                $product = $this->initProduct($remoteProduct, $account);

                if ($product->getIsAccordCadre()) {
                    $properties = $this->mapProperties($remoteProduct['properties']);
                    $product->setProperties($properties);
                }

                if (!$product->getIsAccordCadre()) {
                    $product->setVariants($this->buildVariants($remoteProduct['variants']));
                    $this->setDefaultVariantIdAndOptions($product);
                }

                $productCached->set($product);
                $productCached->expiresAfter(new \DateInterval('PT1H')); // the item will be cached for 1 hour
                $this->cache->save($productCached);
            }
            $products[] = $productCached->get();
        }

        \usort($products, function ($a, $b) {
            return $b->getIsAccordCadre() - $a->getIsAccordCadre();
        });

        return $products;
    }

    public function create(array $data): Product
    {
        $this->cache->clear();
        $productCached = $this->cache->getItem('product_'.$data['id']);
        if (!$productCached->isHit()) {
            $session = $this->requestStack->getSession();
            /** @var Account $account */
            $account = $session->get('account');
            $product = $this->initProduct($data, $account);

            $properties = $this->mapProperties($data['properties']);
            $product->setProperties($properties);

            $categories = [];
            foreach ($data['categories'] as $category) {
                $categories[] = ['id' => $category['id'], 'name' => $category['name']['default']];
            }
            $product->setCategories($categories);

            if (!$product->getIsAccordCadre()) {
                $this->mapProduct($product, $data);
            } else {
                if ($account->getId()) {
                    $this->mapAccordCadre($product, $account);
                }
            }

            $productCached->set($product);
            $productCached->expiresAfter(new \DateInterval('PT1H')); // the item will be cached for 1 hour
            $this->cache->save($productCached);
        }

        return $productCached->get();
    }

    public function buildFilter($remoteFilters): array
    {
        $filters = [];

        if (!empty($remoteFilters['property'])) {
            foreach ($remoteFilters['property'] as $property) {
                if ($property['id'] === 217) {
                    continue;
                }
                $children = [];
                foreach ($property['child'] as $propChild) {
                    $newChild = new Property();
                    $newChild->setId($propChild['id']);
                    $newChild->setName($propChild['name']);
                    $newChild->setValue((string) $propChild['value']);
                    $newChild->setChecked($propChild['checked']);
                    $children[$propChild['value']] = $newChild;
                }

                $filters['properties'][] = [
                    'id' => $property['id'],
                    'name' => $property['name'],
                    'productCount' => $property['count'],
                    'checked' => $property['checked'],
                    'type' => $property['type'],
                    'children' => $children,
                ];
            }
        }

        if (!empty($remoteFilters['company'])) {
            $filters['companies'] = $this->categoryFactory->createAndAddToCollection($remoteFilters['company']);
        }

        if (!empty($remoteFilters['category'])) {
            $filters['categories'] = $this->sellerFactory->createAndAddToCollection($remoteFilters['category']);
        }

        return $filters;
    }

    public function buildParameter($remoteParameter): array
    {
        $parameters = [];
        if ($remoteParameter['name']) {
            $parameters['name'] = $remoteParameter['name'];
        }

        if ($remoteParameter['categories']) {
            foreach ($remoteParameter['categories'] as $category) {
                $parameters['categories'][] = ['id' => $category['id'], 'name' => $category['name']['default']];
            }
        }

        if ($remoteParameter['properties']) {
            foreach ($remoteParameter['properties'] as $property) {
                $parameters['properties'][] = [
                    'property' => $property['property'],
                    'value' => $property['value'],
                ];
            }
        }

        if ($remoteParameter['companies']) {
            foreach ($remoteParameter['companies'] as $company) {
                $parameters['companies'][] = ['id' => $company['id'], 'name' => $company['name']];
            }
        }

        return $parameters;
    }

    private function initProduct(array $data, Account $account): Product
    {
        $product = new Product();
        $product->setId($data['id']);
        $product->setName($data['name']['default']);
        $product->setDescription($data['description']['default'] ?? null);
        $product->setSlug(\sprintf('%s-%d', $data['slug']['default'], $data['id']));
        $product->setQuantity(1);

        $isAccordCadre = $this->checkIsAccordCadre($data['properties']);
        $product->setIsAccordCadre($isAccordCadre);

        if (!$isAccordCadre) {
            $favorites = $this->em->getRepository(Favorite::class)->getFavoritesByAccountAndProductId($account, $data['id']);
            $product->setFavorites($favorites);
        }

        if ($data['company']['id']) {
            $product->setSeller($this->sellerFactory->create($data['company']));
        }

        $images = [];
        foreach ($data['images'] as $image) {
            $images[] = !empty($image['url']) ? $image['url'] : null;
        }
        $product->setImages($images);

        $product->setPrice($this->formatPrice($data['price']));
        $priceReference = \round($data['price_reference'] * 0.01, 2);
        $product->setPriceReference($priceReference);

        return $product;
    }

    private function mapProduct(Product &$product, array $data): void
    {
        $product->setReference($data['reference']);

        $options = [];
        foreach ($data['option_values'] as $option_value) {
            $options[$option_value['option']['name']['default'] ?? ''][] = [
                'parent_id' => $option_value['option']['id'],
                'id' => $option_value['id'],
                'value' => $option_value['value']['default'] ?? null,
            ];
        }
        $product->setOptions($options);
        $product->setVariants($this->buildVariantsOptions($data['variants']));

        if ($product->getPriceReference() && $product->getPrice()) {
            $priceDiff = $product->getPriceReference() - $product->getPrice();
            $percent = \round(($priceDiff * 100) / $product->getPriceReference());
            $product->setPercent($percent);
        }
        $this->setDefaultVariantIdAndOptions($product);
    }

    private function mapAccordCadre(Product &$product, Account $account): void
    {
        $account = $this->em->getRepository(Account::class)->find($account->getId());

        $accordStatut = $this->em->getRepository(AccordStatut::class)->findOneBy([
            'adherent' => $account->getAdherent()->getId(),
            'accordId' => $product->getProperties()['accord-id'],
        ]);

        $status = $accordStatut ? $accordStatut->getStatus() : AccountAccordCadre::PROCESS_STATUS_NOT_ACTIVATED;

        $accountAccordCadre = new AccountAccordCadre();
        $accountAccordCadre->setAccountId($account->getId()->toRfc4122());
        $accountAccordCadre->setStatus($status);
        $accountAccordCadre->setAccordCadreId($product->getId());
        $accountAccordCadre->setAccordId(new Uuid($product->getProperties()['accord-id']));

        $product->setAccountAccordCadre($accountAccordCadre);
    }

    private function checkIsAccordCadre($remoteProperties): bool
    {
        foreach ($remoteProperties as $property) {
            if ($property['property']['name']['default'] === 'accord_cadre') {
                return true;
            }
        }

        return false;
    }

    private function mapProperties($remoteProperties): array
    {
        $properties = [];
        foreach ($remoteProperties as $property) {
            $propertyValue = $property['value'];

            if ($propertyValue === Product::HOME_SELECTION || $propertyValue === Product::HOME_TOP_VENTE) {
                continue;
            }

            if (isset($property['property']['type']) && $property['property']['type'] === 'checkbox') {
                $propertyValue = $property['value'] == 1 ? 'OUI' : 'NON';
            }

            $properties[$property['property']['name']['fr']] = $propertyValue;
        }

        return $properties;
    }

    private function buildVariantsOptions(array $remoteVariants): array
    {
        $options = [];
        foreach ($remoteVariants as $variant) {
            $variantOptions = [];
            if (!empty($variant['option_values'])) {
                foreach ($variant['option_values'] as $option_value) {
                    $variantOptions[] = $option_value['id'];
                }
                $options[] = ['id' => $variant['id'], 'options' => $variantOptions];
            }
        }

        return $options;
    }

    private function buildVariants(array $remoteVariants): array
    {
        $variants = [];
        foreach ($remoteVariants as $variant) {
            $variants[] = ['id' => $variant['id'], 'sku' => $variant['sku']];
        }

        return $variants;
    }

    private function formatPrice($remotePrice): ?float
    {
        return $remotePrice !== null ? UpplerHelper::formatPrice($remotePrice['display_price']) : null;
    }

    private function setDefaultVariantIdAndOptions(Product &$product): void
    {
        $variants = $product->getVariants();
        $firstVariant = \reset($variants);
        $product->setDefaultVariantId($firstVariant['id']);
        $product->setDefaultVariantOptions($firstVariant['options'] ?? []);
    }
}
