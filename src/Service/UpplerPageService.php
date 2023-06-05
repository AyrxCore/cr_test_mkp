<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

class UpplerPageService extends HttpClientProvider
{

    #[Required]
    public RequestStack $requestStack;

    public function getPageById(int $id): string|null
    {
        $session = $this->requestStack->getSession();
        $session->start();

        $res = $this->request(
            'GET',
            $this->apiUrl . 'v1/page/' . $id,
            [],
            true
        );

        if (Response::HTTP_OK === $res->getStatusCode()) {
            $remotePage = json_decode($res->getContent());
            return $remotePage->content->default;
        }

        return null;
    }
}
