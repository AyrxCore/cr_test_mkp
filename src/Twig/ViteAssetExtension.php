<?php

declare(strict_types=1);

namespace App\Twig;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ViteAssetExtension extends AbstractExtension
{
    public const CACHE_KEY = 'vite_manifest';
    private ?array $manifestData = null;

    public function __construct(
        private string $env,
        private string $manifest,
        private CacheItemPoolInterface $cache,
        private HttpClientInterface $client,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('vite_asset', [$this, 'asset'], ['is_safe' => ['html']]),
        ];
    }

    public function asset(string $entry, array $deps)
    {
        if ($this->env === 'dev' && $this->isDevServerRunning()) {
            return $this->assetDev($entry, $deps);
        }

        return $this->assetProd($entry);
    }

    public function assetDev(string $entry, array $deps): string
    {
        if (\in_array('static', $deps, true)) {
            return "http://localhost:3003/assets/{$entry}";
        }

        $html = <<<HTML
                <script type="module" src="http://localhost:3003/assets/@vite/client"></script>
            HTML;

        $html .= <<<HTML
                <script type="module" src="http://localhost:3003/assets/{$entry}" defer></script>
            HTML;

        return $html;
    }

    public function assetProd(string $entry): string
    {
        if ($this->manifestData === null) {
            $item = $this->cache->getItem(self::CACHE_KEY);
            if ($item->isHit()) {
                $this->manifestData = $item->get();
            } else {
                $this->manifestData = \json_decode(\file_get_contents($this->manifest), true);
                $item->set($this->manifestData);
                $this->cache->save($item);
            }
        }
        $file = $this->manifestData[$entry]['file'];
        $css = $this->manifestData[$entry]['css'] ?? [];
        $imports = $this->manifestData[$entry]['imports'] ?? [];
        $html = <<<HTML
            <script type="module" src="/assets/{$file}" defer></script>
        HTML;
        foreach ($css as $cssFile) {
            $html .= <<<HTML
            <link rel="stylesheet" media="screen" href="/assets/{$cssFile}"/>
        HTML;
        }

        foreach ($imports as $import) {
            $html .= <<<HTML
            <link rel="modulepreload" href="/assets/{$import}"/>
        HTML;
        }

        return $html;
    }

    public function isDevServerRunning(): bool
    {
        // Don't expect to have the dev server running when the symfony env is prod
        if ($this->env === 'prod') {
            return false;
        }

        // Check to see if the dev server is actually running by pinging the vite endpoint
        try {
            $response = $this->client->request('GET', 'http://js:3003/assets/@vite/client');

            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            return false;
        }
    }
}
