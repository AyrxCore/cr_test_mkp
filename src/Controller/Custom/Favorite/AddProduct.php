<?php

declare(strict_types=1);

namespace App\Controller\Custom\Favorite;

use App\Service\FavoriteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class AddProduct extends AbstractController
{
    public function __construct(private RequestStack $requestStack, private FavoriteService $favoriteService)
    {
    }

    #[Route(
        path: '/api/favorite-products',
        name: 'add_product_to_favorites_collection',
        methods: ['POST']
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $options = $request->getPayload()->all();
        $productId = (int) $options['productId'] ?? null;
        $variantId = (int) $options['variantId'] ?? null;
        $productName = $options['productName'] ?? null;
        $selectedFavorites = !empty($options['selectedFavorites']) ? $options['selectedFavorites'] : [];
        if (isset($productId, $productName, $variantId, $selectedFavorites)) {
            $this->favoriteService->addProductToFavorites($selectedFavorites, $productId, $variantId, $productName);

            return new JsonResponse(['statut' => 'OK']);
        } else {
            throw new BadRequestException('Impossible d\'ajouter le produit à un ou plusieurs favoris');
        }
    }
}
