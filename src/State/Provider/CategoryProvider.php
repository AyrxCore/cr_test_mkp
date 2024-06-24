<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Factory\CategoryFactory;
use App\Service\UpplerProductService;

readonly class CategoryProvider implements ProviderInterface
{
    public function __construct(private CategoryFactory $categoryFactory, private UpplerProductService $upplerProductService)
    {
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->categoryFactory->createAndAddToCollection($this->upplerProductService->findAllCategories());
    }
}
