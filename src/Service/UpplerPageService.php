<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

class UpplerPageService extends AbstractUpplerService
{
    public function getPageById(int $id): string|null
    {
        $res = $this->request('GET', 'v1/page/'.$id, [], true);

        if ($res->getStatusCode() === Response::HTTP_OK) {
            $remotePage = \json_decode($res->getContent());

            return $remotePage->content->default;
        }

        return null;
    }
}
