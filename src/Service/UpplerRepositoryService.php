<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

// centralise les appels vers uppler pour manipuler des référentiels (countries, ...)
class UpplerRepositoryService extends AbstractUpplerService
{
    public function getCountries($perPage = 200, $page = 1, array $sortings = [], $countries = []): array|null
    {
        $urlSorts = null;

        if (!empty($sortings)) {
            foreach ($sortings as $sorting) {
                $urlSorts .= '&sorting[]='.$sorting;
            }
        }

        $res = $this->request(
            'GET',
            'v1/country?perPage='.$perPage.'&page='.$page.$urlSorts,
            [],
            false,
            false,
            true
        );

        if ($res->getStatusCode() === Response::HTTP_PARTIAL_CONTENT) {
            $headers = $res->getHeaders();
            $paginationArgs = \explode('/', $headers['content-range'][0]);
            $totalItems = $paginationArgs[1];
            $paginationOffset = \explode('-', $paginationArgs[0])[1];

            $newCountries = \json_decode($res->getContent());
            $this->computeCountries($newCountries);
            $countries = \array_merge($countries, $newCountries);
            if ($totalItems > $paginationOffset) {
                ++$page;

                return $this->getCountries(200, $page, $sortings, $countries);
            }

            return $countries;
        }

        return null;
    }

    private function computeCountries(array &$countries): void
    {
        foreach ($countries as $country) {
            $this->computeCountry($country);
        }
    }

    private function computeCountry(&$country)
    {
        $name = $country->name->fr;
        $country->name = $name;
        unset($country->currency);
        unset($country->enabled);
        unset($country->types);
        unset($country->continent);
        unset($country->phone_code);
        unset($country->iso_name);
    }
}
