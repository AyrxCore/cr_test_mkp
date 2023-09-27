<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class UpplerCountryService extends AbstractUpplerService
{
    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function getCountries(int $perPage = 200, int $page = 1, array $countries = []): array
    {
        $path = \sprintf('v1/country?perPage=%d&page=%d', $perPage, $page);
        $res = $this->request(
            method: 'GET',
            path: $path
        );

        if ($res->getStatusCode() !== Response::HTTP_PARTIAL_CONTENT) {
            throw new HttpException(
                Response::HTTP_BAD_REQUEST, // You can use any appropriate HTTP status code here
                'The response status code does not match the expected status code.'
            );
        }

        $headers = $res->getHeaders();
        $paginationArgs = \explode('/', $headers['content-range'][0]);
        $totalItems = $paginationArgs[1];
        $paginationOffset = \explode('-', $paginationArgs[0])[1];

        $newCountries = \json_decode($res->getContent());
        $countries = \array_merge($countries, $newCountries);
        if ($totalItems > $paginationOffset) {
            ++$page;

            return $this->getCountries(page: $page, countries: $countries);
        }

        return $countries;
    }
}
