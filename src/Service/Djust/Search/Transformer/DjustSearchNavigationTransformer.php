<?php

declare(strict_types=1);

namespace App\Service\Djust\Search\Transformer;

class DjustSearchNavigationTransformer
{
    public function transform(array $navigations): array
    {
        $result = [];
        $seen = [];

        foreach ($navigations as $nav) {
            $id = $nav['id'] ?? null;
            $externalId = $nav['externalId'] ?? null;

            // Éviter les doublons et filtrer Root_default
            if ($externalId === 'Root_default' || isset($seen[$externalId])) {
                continue;
            }

            $seen[$externalId] = true;

            $result[] = [
                'id' => $id,
                'name' => $nav['name'] ?? null,
            ];
        }

        return $result;
    }
}
