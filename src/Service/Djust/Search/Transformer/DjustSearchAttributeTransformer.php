<?php

declare(strict_types=1);

namespace App\Service\Djust\Search\Transformer;

class DjustSearchAttributeTransformer
{
    public function transformToAttributeValues(array $attributes): array
    {
        $result = [];

        foreach ($attributes as $attr) {
            $name = $attr['name'] ?? '';
            $externalId = $attr['externalId'] ?? '';
            $value = $attr['value'] ?? null;
            $values = $attr['values'] ?? [];

            $result[] = [
                'attribute' => [
                    'id' => null,
                    'name' => [
                        'fr-FR' => $name,
                        'FR' => $name,
                    ],
                    'type' => !empty($values) ? 'LIST_TEXT' : 'TEXT',
                    'externalId' => $externalId,
                ],
                'value' => !empty($values) ? $values : $value,
            ];
        }

        return $result;
    }
}
