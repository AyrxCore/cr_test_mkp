<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\AccordCadre;
use App\Dto\Price;
use App\Dto\Product;
use App\Dto\Property;
use App\Dto\Seller;
use App\Entity\AccountAccordCadre;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

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

        $res = $this->searchProductsByParams($options, ['properties'], $perPage, $page);
        if (null === $res) {
            return null;
        }

        $products = [];
        foreach ($res->results as $result) {

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

    public function getProduct(int $productId = null, array $filters = [], ?string $accountId = null): Product|null
    {
        $res = $this->getObject($productId, $filters);

        if (null === $res) {
            return null;
        }

        return $this->hydrateProduct($res, $accountId);
    }

    private function hydrateProduct($remoteProduct, $accountId = null)
    {
        $product = new Product();
        $product->setId($remoteProduct->id);
        $product->setName($remoteProduct->name->default);
        $product->setDescription($remoteProduct->description->default ?? null);
        $product->setReference($remoteProduct->reference);
        $categories = [];
        foreach ($remoteProduct->categories as $category) {
            $categories[$category->id] = $category->name->default;
        }
        $product->setCategories($categories);

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

        if ($isAccordCadre) {
            if ($accountId) {
                $accountAccordCadre = $this->em->getRepository(AccountAccordCadre::class)->findOneBy(['accordCadreId' => $remoteProduct->id, 'accountId' => $accountId]);
                if (null === $accountAccordCadre) {
                    $accountAccordCadre = new AccountAccordCadre();
                    $accountAccordCadre->setAccountId($accountId);
                    $accountAccordCadre->setStatus(Product::PROCESS_STATUS_NOT_ACTIVATED);
                    $accountAccordCadre->setAccordCadreId($remoteProduct->id);
                    $this->em->persist($accountAccordCadre);
                    $this->em->flush();
                }

                $product->setAccountAccordCadre($accountAccordCadre);
            }
        } else {
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
                $item = $this->cache->getItem('seller_' . $remoteProduct->company->id);

                if ($item->isHit()) {
                    $seller = $item->get();
                } else {
                    $seller = $this->upplerSellerService->hydrateSeller($remoteProduct->company);
                }

                $product->setSeller($seller);
            }

            #TODO A remplacer par les vraies données après concertation avec JM
            $product->setConditionnement('1');
            $product->setLivraisons(['Franco à partir de 50€ HT de commande - en dessous, 12€ HT de frais de port seront appliqués.']);
        }

        return $product;
    }

    private function hydrateFilters($remoteFilters)
    {
        $filters = [];

        foreach ($remoteFilters->property as $property) {
            if ($property->id === 217) {
                continue;
            }
            $child = [];
            $newChild = new Property();
            $newChild->setId(0);
            $newChild->setName('-- '.$property->name.' --');
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
