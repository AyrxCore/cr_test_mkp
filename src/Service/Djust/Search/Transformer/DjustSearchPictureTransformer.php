<?php

declare(strict_types=1);

namespace App\Service\Djust\Search\Transformer;

class DjustSearchPictureTransformer
{
    public function extractMainImageUrl(array $pictureUrls): ?string
    {
        foreach ($pictureUrls as $picture) {
            if (($picture['main'] ?? false) && ($picture['sizeType'] ?? '') === 'LARGE') {
                return $picture['url'] ?? null;
            }
        }

        return null;
    }

    public function extractProductMediaInfoDTO(array $pictureUrls): array
    {
        $mainUrls = [];
        foreach ($pictureUrls as $picture) {
            if ($picture['main'] ?? false) {
                $mainUrls[] = [
                    'widthInPx' => $picture['widthInPx'] ?? null,
                    'heightInPx' => $picture['heightInPx'] ?? null,
                    'formatType' => $picture['formatType'] ?? null,
                    'sizeType' => $picture['sizeType'] ?? null,
                    'url' => $picture['url'] ?? '',
                ];
            }
        }

        if (empty($mainUrls)) {
            return [];
        }

        return [
            'urls' => $mainUrls,
            'isMain' => true,
        ];
    }

    public function groupPicturesByMain(array $pictureUrls): array
    {
        if (empty($pictureUrls)) {
            return [];
        }

        $grouped = [];
        $urlsByBaseUrl = [];

        // Grouper par URL de base
        foreach ($pictureUrls as $picture) {
            $baseUrl = $this->extractBaseUrl($picture['url'] ?? '');
            $isMain = $picture['main'] ?? false;

            if (!isset($urlsByBaseUrl[$baseUrl])) {
                $urlsByBaseUrl[$baseUrl] = [
                    'isMain' => $isMain,
                    'urls' => [],
                ];
            }

            $urlsByBaseUrl[$baseUrl]['urls'][] = [
                'widthInPx' => $picture['widthInPx'] ?? null,
                'heightInPx' => $picture['heightInPx'] ?? null,
                'formatType' => $picture['formatType'] ?? null,
                'sizeType' => $picture['sizeType'] ?? null,
                'url' => $picture['url'] ?? '',
            ];
        }

        foreach ($urlsByBaseUrl as $data) {
            $grouped[] = [
                'urls' => $data['urls'],
                'isMain' => $data['isMain'],
            ];
        }

        return $grouped;
    }

    private function extractBaseUrl(string $url): string
    {
        $parts = \explode('?', $url);

        return $parts[0] ?? $url;
    }
}
