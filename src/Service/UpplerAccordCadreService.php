<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\AccordCadre;

class UpplerAccordCadreService extends AbstractUpplerProductService
{
    public function getAccordsCadresByParams(array $options = [], array $filters = []): array | null
    {
        $res = $this->searchProductsByParams($options, $filters);

        if (null === $res) {
            return null;
        }

        $accordsCadre = [];
        foreach ($res->results as $result) {
            $accordsCadre[] = $this->getAccordCadre($result->id);
        }
        return $accordsCadre;
    }

    public function getAccordCadre(int $productId): AccordCadre|null
    {
        $this->cache->clear('accord_cadre_' . $productId);
        $item = $this->cache->getItem('accord_cadre_' . $productId);

        if ($item->isHit()) {
            return $item->get();
        }
        $res = $this->getObject($productId);

        if (null === $res) {
            return null;
        }
        $item->set($this->populateAccordCadre($res));
        $item->expiresAfter(new \DateInterval('P1D')); // the item will be cached for 10 seconds
        $this->cache->save($item);

        return $item->get();
    }

    private function populateAccordCadre($remoteAccordCadre)
    {
        $accordCadre = new AccordCadre();
        $accordCadre->setId($remoteAccordCadre->id);
        $accordCadre->setName($remoteAccordCadre->name->default);
        $accordCadre->setDescription($remoteAccordCadre->description->default ?? null);
        $accordCadre->setReference($remoteAccordCadre->reference);

        $categories = [];
        foreach ($remoteAccordCadre->categories as $category) {
            $categories[$category->id] = $category->name->default;
        }
        $accordCadre->setCategories($categories);

        $properties = [];
        foreach($remoteAccordCadre->properties as $property) {
            $properties[$property->property->name->fr] = $property->value;
        }

        $accordCadre->setProperties($properties);
        return $accordCadre;
    }
}
