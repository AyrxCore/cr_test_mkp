<?php

declare(strict_types=1);

namespace App\Controller\Custom\Favorite;

use App\Entity\Favorite;
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
class MoveProduct extends AbstractController
{
    public function __construct(private RequestStack $requestStack, private FavoriteService $favoriteService)
    {
    }

    /**
     * @throws \Exception
     */
    #[Route(
        path: '/api/favorite-products/{id}/favorites',
        name: 'move_product_to_favorites_collection',
        methods: ['POST']
    )]
    public function __invoke(string $id, Request $request): JsonResponse
    {
        $options = $request->request->all();
        $favoriteId = $options['favoriteId'] ?? '';
        try {
            /** @var Favorite $favorite */
            $favorite = $this->favoriteService->moveProductToFavorite($favoriteId, $id);

            if ($favorite) {
                return JsonLdResponse::render('Success', Response::HTTP_OK, \sprintf('Le produit a été déplacé vers la liste %s', $favorite->getName()));
            } else {
                return JsonLdResponse::render('Error', Response::HTTP_NOT_FOUND, "La liste de favori que vous avez sélectionné n'existe pas");
            }
        } catch (\Exception $exception) {
            return JsonLdResponse::render('Error', Response::HTTP_UNPROCESSABLE_ENTITY, $exception->getMessage());
        }
    }
}
