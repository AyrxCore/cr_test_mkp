<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Seller;
use App\Dto\Price;
use App\Dto\Product;
use App\Dto\Property;
use Symfony\Component\HttpFoundation\Response;

class UpplerProductService extends AbstractUpplerProductService
{
    public function getProductsByParams(array $options = [], array $filters = []): array | null
    {
        $res = $this->searchProductsByParams($options, $filters);
        if (null === $res) {
            return null;
        }

        $products = [];
        foreach ($res->results as $result) {
            $products[] = $this->getProduct($result->id);
        }
        return [
            'filters'=> $res->filters,
            'results_count' => $res->results_count,
            'page' => $res->page,
            'results' => $products
        ];
    }

    public function getProduct(int $productId = null, array $filters = []): Product|null
    {
        $res = $this->getObject($productId, $filters);

        if (null === $res) {
            return null;
        }

        return $this->populateProduct($res);
    }

    public function getSeller(int $companyId = null): Seller | null
    {
        $item = $this->cache->getItem('company_' . $companyId);

        if ($item->isHit()) {
            return $item->get();
        }

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

            $item->set($company);
            $item->expiresAfter(new \DateInterval('P1D')); // the item will be cached for 10 seconds
            $this->cache->save($item);

            return $item->get();
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
            $images[] = !empty($image->path) ? $this->upplerUrlSourceListProductImg . $image->path : self::DEFAULT_IMG ;
        }
        $product->setImages($images);

        $options = [];
        foreach ($remoteProduct->option_values as $option_value) {
            $options[$option_value->option->name->default ?? ''][] = [
                'parent_id' => $option_value->option->id,
                'id' => $option_value->id,
                'value' => $option_value->value->default ?? null
            ];
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
            $price->setDisplayPrice(round($remoteProduct->price->display_price * 0.01, 2));
            $price->setFormattedDisplayPrice($remoteProduct->price->formatted_display_price);
            $price->setFormattedDisplayUnitPrice($remoteProduct->price->formatted_display_unit_price);
            $product->setPrice($price);
        }

        if ($product->getPriceReference() && $product->getPrice()) {
            $priceDiff = $product->getPriceReference() - $product->getPrice()->getDisplayPrice();
            $percent = round(($priceDiff * 100) / $product->getPriceReference() );
            $product->setPercent($percent);
        }

        if (isset($remoteProduct->company->id)) {
            $company = $this->getSeller($remoteProduct->company->id);
            $product->setCompany($company);
        }

        #TODO A remplacer par les vraies données après concertation avec JM
        $product->setConditionnement('1');
        $product->setLivraisons(['Franco à partir de 50€ HT de commande - en dessous, 12€ HT de frais de port seront appliqués.']);

        return $product;
    }
}
