<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpplerDynamicEntityService extends AbstractUpplerService
{
    public function getDynamicsEntitiesCategories(array $expands = []): array
    {
        $urlExpands = null;

        if (!empty($expands)) {
            foreach ($expands as $expand) {
                $urlExpands .= $urlExpands === null ? '?expand[]='.$expand : '&expand[]='.$expand;
            }
        }

        $res = $this->request(
            'GET',
            'v1/administrator/dynamic-field-configuration'.$urlExpands,
            [],
            true
        );

        if ($res->getStatusCode() !== Response::HTTP_OK) {
            throw new NotFoundHttpException('Aucun champs dynamique trouvé');
        }

        return \json_decode($res->getContent(), true);
    }

    public function getDynamicsEntities(array $expands = [], array $criteria = []): array
    {
        $params = '?sorting[created_at]=DESC';

        if (!empty($expands)) {
            foreach ($expands as $expand) {
                $params .= '&expand[]='.$expand;
            }
        }

        if (!empty($criteria)) {
            foreach ($criteria as $key => $value) {
                $params .= '&criteria['.$key.']='.$value;
            }
        }

        $res = $this->request(
            'GET',
            'v1/administrator/dynamic-entity'.$params,
            [],
            true
        );

        if (!$res || $res->getStatusCode() !== Response::HTTP_OK) {
            throw new NotFoundHttpException('Not Found');
        }

        return \json_decode($res->getContent(), true);
    }
}
