<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Seller;
use App\Dto\Price;
use App\Dto\Product;
use App\Dto\Property;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Service\Attribute\Required;

class UpplerProductService extends HttpClientProvider
{
    private const DEFAULT_IMG = '/vuejs/assets/img/default-image.png';
    private string $upplerUrlSourceProductImg;
    private string $upplerUrlSourceSellerImg;
    public function __construct(
        string $env,
        string $apiUrl,
        string $adminClientId,
        string $adminClientSecret,
        string $adminTokenFile,
        string $httpCachePath,
        string $upplerUrlSourceProductImg,
        string $upplerUrlSourceSellerImg
    )
    {
        parent::__construct($env, $apiUrl, $adminClientId, $adminClientSecret, $adminTokenFile, $httpCachePath);
        $this->upplerUrlSourceProductImg = $upplerUrlSourceProductImg;
        $this->upplerUrlSourceSellerImg = $upplerUrlSourceSellerImg;
    }

    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public EntityManagerInterface $em;

    public function searchProductsByParams(array $options = [], array $filters = []): array | null
    {
        $session = $this->requestStack->getSession();
        $session->start();

        $urlFilters = null;

        if (!empty($filters)) {
            foreach ($filters as $filter) {
                $urlFilters.= null === $urlFilters ? '?expand[]=' . $filter : '&expand[]=' . $filter;
            }
        }

        $res = $this->request(
            'POST',
            $this->apiUrl . 'v1/buyer/search/product'. $urlFilters,
            [
                'json' => $options
            ]
        );

        if (Response::HTTP_OK === $res->getStatusCode()) {
            $upplerProductsData =  json_decode($res->getContent());
            $products = [];
            foreach ($upplerProductsData->results as $result) {
                $products[] = $this->getProduct($result->id);
            }
            return [
                'filters'=> $upplerProductsData->filters,
                'results_count' => $upplerProductsData->results_count,
                'page' => $upplerProductsData->page,
                'results' => $products
            ];
        }

        return null;
    }

    public function getProduct(int $productId = null, array $filters = []): Product
    {
        $session = $this->requestStack->getSession();
        $session->start();

        $filters =  empty($filters) ? ['price', 'properties', 'variants'] : $filters;

        $urlFilters = null;

        if (!empty($filters)) {
            foreach ($filters as $filter) {
                $urlFilters.= null === $urlFilters ? '?expand[]=' . $filter : '&expand[]=' . $filter;
            }
        }

        $res = $this->request(
            'GET',
            $this->apiUrl . 'v1/buyer/product/' . $productId . $urlFilters
        );

        if (Response::HTTP_OK === $res->getStatusCode()) {
            return $this->populateProduct(json_decode($res->getContent()));
        } else {
            throw new NotFoundHttpException('Aucun produit trouvé');
        }

        return [];
    }

    public function getSeller(int $companyId = null): Seller | null
    {
        $session = $this->requestStack->getSession();
        $session->start();

        $res = $this->request(
            'GET',
            $this->apiUrl . 'v1/buyer/seller/' . $companyId
        );

        if (Response::HTTP_OK === $res->getStatusCode()) {
            $upplerCompany = json_decode($res->getContent());

            $company = new Seller();
            $company->setId($upplerCompany->id);
            $company->setName($upplerCompany->name);
            $company->setCorporateName($upplerCompany->corporate_name);
            $avatar = !empty($upplerCompany->avatar) ? $this->upplerUrlSourceSellerImg . $upplerCompany->avatar : self::DEFAULT_IMG;
            $company->setAvatar($avatar);
            $description = !empty($upplerCompany->description->default) ? $upplerCompany->description->default : null;
            $company->setDescription($description);
            return $company;
        }

        return null;
    }

    private function populateProduct($remoteProduct)
    {
        $product = new Product();
        $product->setId($remoteProduct->id);
        $categories = [];
        foreach ($remoteProduct->categories as $category) {
            $categories[$category->id] = $category->name->default;
        }

        $product->setCategories($categories);
        $product->setName($remoteProduct->name->default);
        $product->setDescription($remoteProduct->description->default ?? null);
        $product->setReference($remoteProduct->reference);
        $priceReference = round($remoteProduct->price_reference * 0.01, 2);
        $product->setPriceReference($priceReference);

        $images = [];
        foreach ($remoteProduct->images as $image) {
            $images[] = !empty($image->path) ? $this->upplerUrlSourceProductImg . $image->path : self::DEFAULT_IMG ;
        }
        $product->setImages($images);

        $options = [];
        foreach ($remoteProduct->option_values as $option_value) {
            $options[$option_value->id] = $option_value->value->default ?? null;
        }
        $product->setOptions($options);

        $properties = [];

        foreach ($remoteProduct->properties as $property) {
            $newProperty = new Property();
            $newProperty->setId($property->id);
            $newProperty->setName($property->property->name->default ?? null);
            $newProperty->setValue($property->value);
            $properties[] = $newProperty;
        }
        $product->setProperties($properties);

        if (null !== $remoteProduct->price) {
            $price = new Price();
            $price->setId($remoteProduct->price->id);
            $price->setAmount($remoteProduct->price->amount);
            $price->setDisplayPrice($remoteProduct->price->display_price);
            $price->setFormattedDisplayPrice($remoteProduct->price->formatted_display_price);
            $price->setFormattedDisplayUnitPrice($remoteProduct->price->formatted_display_unit_price);
            $product->setPrice($price);
        }

        if (null !== $remoteProduct->base_price) {
            $basePrice = new Price();
            $basePrice->setId($remoteProduct->base_price->id);
            $basePrice->setAmount($remoteProduct->base_price->amount);
            $basePrice->setDisplayPrice($remoteProduct->base_price->display_price);
            $basePrice->setFormattedDisplayPrice($remoteProduct->base_price->formatted_display_price);
            $basePrice->setFormattedDisplayUnitPrice($remoteProduct->base_price->formatted_display_unit_price);
            $product->setBasePrice($basePrice);
        }


        if (isset($remoteProduct->company->id)) {
            $company = $this->getSeller($remoteProduct->company->id);
            $product->setCompany($company);
        }

        return $product;
    }
}
