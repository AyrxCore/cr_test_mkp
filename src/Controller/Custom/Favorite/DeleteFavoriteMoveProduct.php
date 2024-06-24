<?php

declare(strict_types=1);

namespace App\Controller\Custom\Favorite;

use App\Helper\JsonLdResponse;
use App\Service\FavoriteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class DeleteFavoriteMoveProduct extends AbstractController
{
    public function __construct(private RequestStack $requestStack, private FavoriteService $favoriteService)
    {
    }

    /**
     * @throws \Exception
     */
    #[Route(
        path: '/api/favorite-products/favorites',
        name: 'move_products_to_other_favorites_collection',
        methods: ['POST']
    )]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $options = $request->getPayload()->all();

            $favoriteId = $options['favoriteId'] ?? null;
            $favoriteIdToReceive = $options['favoriteIdToReceive'] ?? null;

            $this->favoriteService->moveProductsToOtherFavorite($favoriteId, $favoriteIdToReceive);

            return JsonLdResponse::render('Success', Response::HTTP_OK, 'La liste a bien été supprimée et les produits déplacés');
        } catch (\Exception $exception) {
            return JsonLdResponse::render('Error', Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }
}
