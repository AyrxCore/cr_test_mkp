<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpplerDynamicEntityService extends AbstractUpplerService
{
    public function getDynamicsEntitiesCategories(array $expands = []): array
    {
        $path = 'v1/administrator/dynamic-field-configuration';

        if (!empty($expands)) {
            foreach ($expands as $expand) {
                $path .= \sprintf('&expand[]=%s', $expand);
            }
        }

        $res = $this->request(
            method: 'GET',
            path: $path,
            isAdmin: true
        );

        if ($res->getStatusCode() !== Response::HTTP_OK && $res->getStatusCode() !== Response::HTTP_PARTIAL_CONTENT) {
            throw new NotFoundHttpException('Not found');
        }

        $dynamicsFields = \json_decode($res->getContent(), true);

        $data = [];
        // Cet appel API nous retourne une liste de données
        // mais nous n'avons besoin des propriétés category_name et category_color pour construire la liste des catégories
        foreach ($dynamicsFields as $dynamicField) {
            if ($dynamicField['name']['fr'] === 'category_name') {
                foreach ($dynamicField['dynamic_field_choice'] as $key => $choice) {
                    $data[$key]['id'][] = $key;
                    $data[$key]['name'][] = $choice['value'];
                }
            } elseif ($dynamicField['name']['fr'] === 'category_color') {
                foreach ($dynamicField['dynamic_field_choice'] as $key => $choice) {
                    $data[$key]['color'][] = $choice['value'];
                }
            }
        }

        return $data;
    }

    public function getDynamicsEntities(array $expands = [], array $criteria = []): array
    {
        $path = 'v1/administrator/dynamic-entity?sorting[created_at]=DESC';

        if (!empty($expands)) {
            foreach ($expands as $expand) {
                $path .= \sprintf('&expand[]=%s', $expand);
            }
        }

        if (!empty($criteria)) {
            foreach ($criteria as $key => $value) {
                $path .= \sprintf('&criteria[%s]=%s', $key, $value);
            }
        }

        $res = $this->request(
            method: 'GET',
            path: $path,
            isAdmin: true
        );

        if (!$res || $res->getStatusCode() !== Response::HTTP_OK && $res->getStatusCode() !== Response::HTTP_PARTIAL_CONTENT) {
            throw new NotFoundHttpException('Not Found');
        }

        return \json_decode($res->getContent(), true);
    }
}
