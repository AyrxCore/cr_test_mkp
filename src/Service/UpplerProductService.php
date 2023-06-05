<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Price;
use App\Dto\Product;
use App\Dto\Property;
use App\Entity\Account;
use App\Entity\AccordStatut;
use App\Dto\AccountAccordCadre;
use App\Entity\Favorite;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Service\Attribute\Required;

class UpplerProductService extends HttpClientProvider
{

    protected AdapterInterface $cache;

    protected UpplerSellerService $upplerSellerService;

    public function __construct(
        string $env,
        string $apiUrl,
        string $adminClientId,
        string $adminClientSecret,
        string $adminTokenFile,
        string $httpCachePath,
        AdapterInterface $cache,
        UpplerSellerService $upplerSellerService
    ) {
        parent::__construct($env, $apiUrl, $adminClientId, $adminClientSecret, $adminTokenFile, $httpCachePath);
        $this->cache = $cache;
        $this->upplerSellerService = $upplerSellerService;
    }

    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public EntityManagerInterface $em;

    public function findProductsByOptions(
        array $options,
        array $params = [],
        int $page = 1,
        int $perPage = 10,
        bool $showFilters = false
    ): array|null {
        $session = $this->requestStack->getSession();
        $session->start();

        $expandParams = null;
        $params = empty($params) ? ['price', 'properties', 'variants', 'company'] : $params;

        if (!empty($params)) {
            foreach ($params as $param) {
                $expandParams .= '&expand[]=' . $param;
            }
        }

        $res = $this->request(
            'POST',
            $this->apiUrl . 'v1/buyer/search/product?page=' . $page . '&perPage=' . $perPage . $expandParams,
            [
                'json' => $options,
            ]
        );

        if (Response::HTTP_OK !== $res->getStatusCode()) {
            return null;
        }
        $remoteProducts = json_decode($res->getContent());

        $products = [];
        $session = $this->requestStack->getSession();

        foreach ($remoteProducts->results as $remoteProduct) {
            try {
                $products[] = $this->hydrateProductFromList($remoteProduct, $session->get('account'));
            } catch (InvalidArgumentException $e) {
                $this->apiLogger->error($e->getMessage());
            }
        }

        if ($showFilters) {
            usort($products, function ($a, $b) {
                return $b->isAccordCadre() - $a->isAccordCadre();
            });
            return [
                'filters'       => $this->hydrateFilters($remoteProducts->filters),
                'results_count' => $remoteProducts->results_count,
                'page'          => $remoteProducts->page,
                'results'       => $products,
                'parameters'    => $remoteProducts->parameters,
            ];
        } else {
            return $products;
        }
    }

    public function findProductById(int $productId = null, array $filters = [], ?string $accountId = null): Product|null
    {
        $session = $this->requestStack->getSession();
        $session->start();

        $filters = empty($filters) ? ['price', 'properties', 'variants', 'company'] : $filters;
        $urlFilters = null;

        if (!empty($filters)) {
            foreach ($filters as $filter) {
                $urlFilters .= null === $urlFilters ? '?expand[]=' . $filter : '&expand[]=' . $filter;
            }
        }

        $res = $this->request(
            'GET',
            $this->apiUrl . 'v1/buyer/product/' . $productId . $urlFilters
        );

        if (Response::HTTP_OK !== $res->getStatusCode()) {
            throw new NotFoundHttpException('Produit avec l\'Id: ' . $productId . ' n\' a pas été trouvé');
        }

        $product = json_decode($res->getContent());

        return $this->hydrateProduct($product, $accountId);
    }

    public function findVariantById(int $variantId = null)
    {
        $session = $this->requestStack->getSession();
        $session->start();

        $filters = ['price'];
        $urlFilters = null;

        if (!empty($filters)) {
            foreach ($filters as $filter) {
                $urlFilters .= null === $urlFilters ? '?expand[]=' . $filter : '&expand[]=' . $filter;
            }
        }

        $res = $this->request(
            'GET',
            $this->apiUrl . 'v1/buyer/variant/' . $variantId . $urlFilters
        );

        if (Response::HTTP_OK !== $res->getStatusCode()) {
            throw new NotFoundHttpException('L\'option avec l\'Id: ' . $variantId . ' n\' a pas été trouvé');
        }

        return json_decode($res->getContent());
    }

    public function findAllCategories(string $accountId, int $page = 1, int $perPage = 1): \stdClass|null
    {
        $this->cache->clear();
        $item = $this->cache->getItem('categories_' . $accountId);
        if (!$item->isHit()) {
            $session = $this->requestStack->getSession();
            $session->start();

            $res = $this->request(
                'POST',
                $this->apiUrl . 'v1/buyer/search/product?page=' . $page . '&perPage=' . $perPage,
            );

            if (Response::HTTP_OK !== $res->getStatusCode()) {
                throw new NotFoundHttpException('Aucune catégorie n\' a été trouvée');
            }
            $remoteResults = json_decode($res->getContent());

            $item->set($remoteResults->filters->category);
            $item->expiresAfter(new \DateInterval('P1D')); // the item will be cached for 1 day
            $this->cache->save($item);
        }

        return $item->get();
    }

    /**
     * @param $remoteProduct
     *
     * @return Product
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function hydrateProductFromList($remoteProduct, Account $account): Product
    {
        $product = new Product();
        $this->initHydrateProduct($remoteProduct, $product, $account, true);
        $product->setVariants($remoteProduct->variants);

        if (!$product->isAccordCadre()) {
            $priceReference = round($remoteProduct->price_reference * 0.01, 2);
            $product->setPriceReference($priceReference);
            $images[] = !empty($remoteProduct->images[0]->url) ? $remoteProduct->images[0]->url : null;
            $product->setImages($images);

            $this->formatPrice($remoteProduct, $product);
        }

        return $product;
    }

    private function hydrateProduct($remoteProduct, $accountId = null): Product
    {
        $session = $this->requestStack->getSession();
        $product = new Product();
        $this->initHydrateProduct($remoteProduct, $product, $session->get('account'));

        if ($product->isAccordCadre()) {
            if ($accountId) {
                $properties = [];
                foreach ($remoteProduct->properties as $property) {
                    $properties[$property->property->name->fr] = $property->value;
                }
                $product->setProperties($properties);


                $account = $this->em->getRepository(Account::class)->find($accountId);

                $accordStatut = $this->em->getRepository(AccordStatut::class)->findOneBy([
                    'adherent' => $account->getAdherent()->getId(),
                    'accordId' => $properties['accord-id'],
                ]);

                $status = $accordStatut ? $accordStatut->getStatus() : AccountAccordCadre::PROCESS_STATUS_NOT_ACTIVATED;

                $accountAccordCadre = new AccountAccordCadre();
                $accountAccordCadre->setAccountId($accountId);
                $accountAccordCadre->setStatus($status);
                $accountAccordCadre->setAccordCadreId($remoteProduct->id);
                $accountAccordCadre->setAccordId(new Uuid($properties['accord-id']));

                $product->setAccountAccordCadre($accountAccordCadre);
            }
        } else {
            $priceReference = round($remoteProduct->price_reference * 0.01, 2);
            $product->setPriceReference($priceReference);

            $images = [];
            foreach ($remoteProduct->images as $image) {
                $images[] = !empty($image->url) ? $image->url : null;
            }
            $product->setImages($images);

            $options = [];
            foreach ($remoteProduct->option_values as $option_value) {
                $options[$option_value->option->name->default ?? ''][] = [
                    'parent_id' => $option_value->option->id,
                    'id'        => $option_value->id,
                    'value'     => $option_value->value->default ?? null,
                ];
            }
            $product->setOptions($options);

            $variants = [];
            foreach ($remoteProduct->variants as $variant) {
                $variantOptions = [];
                if (!empty($variant->option_values)) {
                    foreach ($variant->option_values as $option_value) {
                        $variantOptions[] = $option_value->id;
                    }
                    $variants[$variant->id] = $variantOptions;
                }
            }
            if (!empty($variants)) {
                $product->setVariants($variants);
            }

            $this->formatPrice($remoteProduct, $product);

            if ($product->getPriceReference() && $product->getPrice()) {
                $priceDiff = $product->getPriceReference() - $product->getPrice();
                $percent = round(($priceDiff * 100) / $product->getPriceReference());
                $product->setPercent($percent);
            }

            #TODO A remplacer par les vraies données après concertation avec JM
            $product->setConditionnement('1');
            $product->setLivraisons(
                ['Franco à partir de 50€ HT de commande - en dessous, 12€ HT de frais de port seront appliqués.']
            );
        }

        return $product;
    }

    private function hydrateFilters($remoteFilters): array
    {
        $filters = [];
        if (!empty($remoteFilters->company)) {
            $filters['companies'] = $remoteFilters->company;
        }

        if (!empty($remoteFilters->property)) {
            foreach ($remoteFilters->property as $property) {
                if ($property->id === 217) {
                    continue;
                }
                $child = [];
                foreach ($property->child as $propChild) {
                    $newChild = new Property();
                    $newChild->setId($propChild->id);
                    $newChild->setName($propChild->name);
                    $newChild->setValue((string)$propChild->value);
                    $newChild->setChecked($propChild->checked);
                    $child[$propChild->value] = $newChild;
                }

                $filters['properties'][] = [
                    'id'      => $property->id,
                    'name'    => $property->name,
                    'count'   => $property->count,
                    'checked' => $property->checked,
                    'type'    => $property->type,
                    'child'   => $child,
                ];
            }
        }


        if (!empty($remoteFilters->category)) {
            $filters['categories'] = $remoteFilters->category;
        }

        return $filters;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function initHydrateProduct($remoteProduct, Product $product, Account $account, $fromList = false): void
    {
        $product->setId($remoteProduct->id);
        $product->setName($remoteProduct->name->default);
        $product->setDescription($remoteProduct->description->default ?? null);
        $product->setReference($remoteProduct->reference);
        $product->setSlug($remoteProduct->slug->default);
        $favorites = $this->em->getRepository(Favorite::class)->getFavoritesByAccountAndProducId($account, $remoteProduct->id);
        $product->setFavorites($favorites);
        $this->populatePropertiesAndSetIfIsAccordCadre($remoteProduct, $product);
        $categories = [];

        foreach ($remoteProduct->categories as $category) {
            $categories[$category->id] = $category->name->default;
        }
        $product->setCategories($categories);
        if ($remoteProduct->company->id) {
            $item = $this->cache->getItem('seller_' . $remoteProduct->company->id);

            if ($item->isHit()) {
                $seller = $item->get();
            } else {
                $seller = $fromList
                    ?
                    $this->upplerSellerService->hydrateSeller($remoteProduct->company)
                    :
                    $this->upplerSellerService->getSeller($remoteProduct->company->id);
            }

            $product->setSeller($seller);
        }
    }

    /**
     * @param $remoteProduct
     * @param  Product  $product
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function populatePropertiesAndSetIfIsAccordCadre($remoteProduct, Product $product)
    {
        $properties = [];
        $isAccordCadre = false;
        foreach ($remoteProduct->properties as $property) {
            if ('accord_cadre' === $property->property->name->default) {
                $isAccordCadre = true;
            }

            if (isset($property->property->type) && $property->property->type === "checkbox") {
                $propertyValue = $property->value == 1 ? "OUI" : "NON";
            } else {
                $propertyValue = $property->value;
            }
            $properties[$property->property->name->fr] = $propertyValue;
        }
        $product->setProperties($properties);
        $product->setIsAccordCadre($isAccordCadre);
    }

    /**
     * @param $remoteProduct
     * @param  Product  $product
     *
     * @return void
     */
    private function formatPrice($remoteProduct, Product $product): void
    {
        if (null !== $remoteProduct->price) {
            $price = round($remoteProduct->price->display_price * 0.01, 2);
            $product->setPrice($price);
        }
    }

}
