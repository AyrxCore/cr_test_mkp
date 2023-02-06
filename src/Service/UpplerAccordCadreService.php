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
        $res = $this->getObject($productId);

        if (null === $res) {
            return null;
        }

        return $this->populateAccordCadre($res);
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
