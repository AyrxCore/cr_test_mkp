<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpplerDynamicEntityService extends AbstractUpplerService
{
    public function getDynamicsEntitiesCategories(array $expands = []): array
    {
        $path = $this->buildCategoriesPath($expands);
        $response = $this->request(
            method: 'GET',
            path: $path,
            isAdmin: true
        );

        $this->validateResponse($response);
        $dynamicsFields = \json_decode($response->getContent(), true);

        return $this->processCategoriesData($dynamicsFields);
    }

    public function getDynamicsEntities(
        array $expands = [],
        array $criteria = [],
        ?string $dynamicEntityConfigurationId = null
    ): array {
        $path = $this->buildEntitiesPath($dynamicEntityConfigurationId, $expands, $criteria);

        $response = $this->request(
            method: 'GET',
            path: $path
        );

        $this->validateResponse($response);

        return \json_decode($response->getContent(), true);
    }

    private function buildCategoriesPath(array $expands): string
    {
        $path = 'v1/administrator/dynamic-field-configuration';

        foreach ($expands as $expand) {
            $path .= \sprintf('&expand[]=%s', \urlencode($expand));
        }

        return $path;
    }

    private function validateResponse($response): void
    {
        if (!$response || !\in_array($response->getStatusCode(), [Response::HTTP_OK, Response::HTTP_PARTIAL_CONTENT], true)) {
            throw new NotFoundHttpException('Not Found');
        }
    }

    private function processCategoriesData(array $dynamicsFields): array
    {
        $data = [];

        foreach ($dynamicsFields as $dynamicField) {
            if ($dynamicField['name']['fr'] === 'category_name') {
                $this->processCategoryNames($data, $dynamicField);
            } elseif ($dynamicField['name']['fr'] === 'category_color') {
                $this->processCategoryColors($data, $dynamicField);
            }
        }

        return $data;
    }

    private function processCategoryNames(array &$data, array $dynamicField): void
    {
        foreach ($dynamicField['dynamic_field_choice'] as $key => $choice) {
            $data[$key]['id'][] = $key;
            $data[$key]['name'][] = $choice['value'];
        }
    }

    private function processCategoryColors(array &$data, array $dynamicField): void
    {
        foreach ($dynamicField['dynamic_field_choice'] as $key => $choice) {
            $data[$key]['color'][] = $choice['value'];
        }
    }

    private function buildEntitiesPath(
        ?string $dynamicEntityConfigurationId,
        array $expands,
        array $criteria
    ): string {
        $path = 'v1/buyer/dynamic-entity-configuration';

        if ($dynamicEntityConfigurationId) {
            $path .= "/{$dynamicEntityConfigurationId}/entities";
        }

        $path .= '?sorting[created_at]=DESC';

        foreach ($expands as $expand) {
            $path .= \sprintf('&expand[]=%s', \urlencode($expand));
        }

        foreach ($criteria as $key => $value) {
            $path .= \sprintf('&criteria[%s]=%s', \urlencode($key), \urlencode($value));
        }

        return $path;
    }
}
