<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Price;
use App\Dto\Product;
use App\Dto\Property;

class UpplerProductService extends AbstractUpplerProductService
{
    public function getProductsByParams(array $options = [], array $filters = [], int $perPage = 5, $page = 1): array | null
    {
        $showFilters = false;

        if (!empty($options['with_filter'])) {
            $showFilters = true;
            unset($options['with_filter']);
            $perPage = 12;
        }

        $res = $this->searchProductsByParams($options, $filters, $perPage, $page);
        if (null === $res) {
            return null;
        }

        $products = [];
        foreach ($res->results as $result) {
            if (str_contains($result->reference, 'fat-')) {
                continue;
            }
            $products[] = $this->getProduct($result->id);
        }

        if ($showFilters) {
            return [
                'filters'=> $this->hydrateFilters($res->filters),
                'results_count' => $res->results_count,
                'page' => $res->page,
                'results' => $products
            ];
        } else {
            return $products;
        }

    }

    public function getProduct(int $productId = null, array $filters = []): Product|null
    {
        $res = $this->getObject($productId, $filters);

        if (null === $res) {
            return null;
        }

        return $this->hydrateProduct($res);
    }

    private function hydrateProduct($remoteProduct)
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
            $images[] = !empty($image->path) ? $image->path : null ;
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
            $company = $this->upplerSellerService->getSeller($remoteProduct->company->id);
            $product->setSeller($company);
        }

        #TODO A remplacer par les vraies données après concertation avec JM
        $product->setConditionnement('1');
        $product->setLivraisons(['Franco à partir de 50€ HT de commande - en dessous, 12€ HT de frais de port seront appliqués.']);

        return $product;
    }

    private function hydrateFilters($remoteFilters)
    {
        $filters = [];

        foreach ($remoteFilters->property as $property) {
            $child = [];
            foreach ($property->child as $propChild) {
                $newChild = new Property();
                $newChild->setId($propChild->id);
                $newChild->setName($propChild->name);
                $newChild->setValue($propChild->value);
                $newChild->setChecked($propChild->checked);
                $child[] = $newChild;
            }

            $filters['properties'][] = [
                'id' => $property->id,
                'name'=> $property->name,
                'count'=> $property->count,
                'checked'=> $property->checked,
                'type'=> $property->type,
                'child'=> $child,
            ];
        }

        return $filters;

    }
}
