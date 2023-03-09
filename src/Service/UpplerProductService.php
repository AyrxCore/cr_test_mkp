<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Price;
use App\Dto\Product;
use App\Dto\Property;
use App\Entity\Account;
use App\Entity\AccordStatut;
use App\Dto\AccountAccordCadre;
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
        foreach ($remoteProducts->results as $remoteProduct) {
            try {
                $products[] = $this->hydrateProductFromList($remoteProduct);
            } catch (InvalidArgumentException $e) {
                $this->apiLogger->error($e->getMessage());
            }
        }

        if ($showFilters) {
            return [
                'filters'       => $this->hydrateFilters($remoteProducts->filters),
                'results_count' => $remoteProducts->results_count,
                'page'          => $remoteProducts->page,
                'results'       => $products,
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

    /**
     * @param $remoteProduct
     *
     * @return Product
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function hydrateProductFromList($remoteProduct): Product
    {
        $product = new Product();
        $this->initHydrateProduct($remoteProduct, $product);

        $isAccordCadre = $this->isAccordCadre($remoteProduct, $product);

        if (!$isAccordCadre) {
            $priceReference = round($remoteProduct->price_reference * 0.01, 2);
            $product->setPriceReference($priceReference);
            $images[] = !empty($remoteProduct->images[0]->url) ? $remoteProduct->images[0]->url : null;
            $product->setImages($images);

            $this->hydratePrice($remoteProduct, $product);
        }

        return $product;
    }

    private function hydrateProduct($remoteProduct, $accountId = null): Product
    {
        $product = new Product();
        $this->initHydrateProduct($remoteProduct, $product);

        $categories = [];

        foreach ($remoteProduct->categories as $category) {
            $categories[$category->id] = $category->name->default;
        }
        $product->setCategories($categories);

        $isAccordCadre = $this->isAccordCadre($remoteProduct, $product);

        if ($isAccordCadre) {
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

            $this->hydratePrice($remoteProduct, $product);

            if ($product->getPriceReference() && $product->getPrice()) {
                $priceDiff = $product->getPriceReference() - $product->getPrice()->getDisplayPrice();
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

        foreach ($remoteFilters->property as $property) {
            if ($property->id === 217) {
                continue;
            }
            $child = [];
            $newChild = new Property();
            $newChild->setId(0);
            $newChild->setName('-- ' . $property->name . ' --');
            $newChild->setValue('');
            $newChild->setChecked(null);
            $child[] = $newChild;
            foreach ($property->child as $propChild) {
                $newChild = new Property();
                $newChild->setId($propChild->id);
                $newChild->setName($propChild->name);
                $newChild->setValue($propChild->value);
                $newChild->setChecked($propChild->checked);
                $child[] = $newChild;
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

        return $filters;
    }

    private function initAccordCadre(string $accountId, $remoteProduct): AccountAccordCadre
    {
        $accountAccordCadre = $this->em->getRepository(AccountAccordCadre::class)->findOneBy(
            ['accordCadreId' => $remoteProduct->id, 'accountId' => $accountId]
        );
        if (null === $accountAccordCadre) {
            $accountAccordCadre = new AccountAccordCadre();
            $accountAccordCadre->setAccountId($accountId);
            $accountAccordCadre->setStatus(Product::PROCESS_STATUS_NOT_ACTIVATED);
            $accountAccordCadre->setAccordCadreId($remoteProduct->id);
            $this->em->persist($accountAccordCadre);
            $this->em->flush();
        }

        return $accountAccordCadre;
    }

    private function initHydrateProduct($remoteProduct, $product)
    {
        $product->setId($remoteProduct->id);
        $product->setName($remoteProduct->name->default);
        $product->setDescription($remoteProduct->description->default ?? null);
        $product->setReference($remoteProduct->reference);
        if ($remoteProduct->company->id) {
            $item = $this->cache->getItem('seller_' . $remoteProduct->company->id);

            if ($item->isHit()) {
                $seller = $item->get();
            } else {
                $seller = $this->upplerSellerService->hydrateSeller($remoteProduct->company);
            }

            $product->setSeller($seller);
        }
    }

    /**
     * @param $remoteProduct
     * @param  Product  $product
     *
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function isAccordCadre($remoteProduct, Product $product): bool
    {
        $properties = [];
        $isAccordCadre = false;
        foreach ($remoteProduct->properties as $property) {
            if ('accord_cadre' === $property->property->name->default) {
                $isAccordCadre = true;
            }
            $properties[$property->property->name->fr] = $property->value;
        }
        $product->setProperties($properties);
        $product->setIsAccordCadre($isAccordCadre);

        return $isAccordCadre;
    }

    /**
     * @param $remoteProduct
     * @param  Product  $product
     *
     * @return void
     */
    private function hydratePrice($remoteProduct, Product $product): void
    {
        if (null !== $remoteProduct->price) {
            $price = new Price();
            $price->setDisplayPrice(round($remoteProduct->price->display_price * 0.01, 2));
            $price->setFormattedDisplayPrice($remoteProduct->price->formatted_display_price);
            $product->setPrice($price);
        }
    }

}
