<?php
namespace App\Controller\Api\Buyer;

use App\Entity\Favorite;
use App\Service\FavoriteService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Service\Attribute\Required;

#[Route("/api/favorites")]
class FavoriteApiController extends AbstractController
{
    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public SerializerInterface $serializer;

    #[Required]
    public FavoriteService $favoriteService;

    /**
     * @Route("/{id}/products", name="get_favorite_products", methods={"GET"})
     */
    public function __invoke(Favorite $favorite, NormalizerInterface $normalizer)
    {
        $jsonFavorite = $normalizer->normalize($favorite, 'json', ['groups' => 'favorite:get']);

        return new JsonResponse($jsonFavorite);
    }

    #[Route('/item/add', name: 'add_favorite_item', methods: ['POST'])]
    public function addItemFavorites(Request $request): JsonResponse
    {
        $session = $this->requestStack->getSession();
        $session->start();

        if (!$session->has('account') || empty($session->get('account'))) {
            return new JsonResponse('session account is not hydrated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $options = $request->request->all();
        $productId = (int)$options['productId'] ?? null;
        $variantId = (int)$options['variantId'] ?? null;
        $productName = $options['productName'] ?? null;
        $selectedFavorites = !empty($options['selectedFavorites']) ? $options['selectedFavorites'] : null;
        if (isset($productId, $productName, $variantId, $selectedFavorites)){
            $this->favoriteService->addItemToFavorites($selectedFavorites, $productId, $variantId, $productName);
            return new JsonResponse('Ajout du produit à toutes les listes sélectionnées effectué');
        } else {
            throw new Exception('Impossible d\'ajouter le produit à un ou plusieurs favoris');
        }
    }

    /**
     * @throws Exception
     */
    #[Route('/item/remove/{favoriteId}/{upplerProductId}', name: 'remove_favorite_item', methods: ['DELETE'])]
    public function removeFavoriteItem($favoriteId, $upplerProductId): JsonResponse
    {
        $session = $this->requestStack->getSession();
        $session->start();

        if (!$session->has('account') || empty($session->get('account'))) {
            return new JsonResponse('session account is not hydrated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $favorite = $this->favoriteService->removeItemFromFavorites($favoriteId, $upplerProductId);

        return new JsonResponse($favorite);
    }

    /**
     * @throws Exception
     */
    #[Route('/item/move', name: 'item_move', methods: ['POST'])]
    public function moveItemToOtherFavorite(Request $request): JsonResponse
    {
        $session = $this->requestStack->getSession();
        $session->start();

        if (!$session->has('account') || empty($session->get('account'))) {
            return new JsonResponse('session account is not hydrated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $options = $request->request->all();
        $favoriteIdToReceive= $options['favoriteIdToReceive'] ?? null;
        $upplerProductId= $options['upplerProductId'] ?? null;
        if (isset($favoriteIdToReceive, $favoriteIdToReceive)){
            $this->favoriteService->moveItemToFavorite($favoriteIdToReceive, $upplerProductId);
            return new JsonResponse(true);
        } else {
            throw new Exception('Impossible de déplacer ce Item');
        }
    }

    /**
     * @throws Exception
     */
    #[Route('/items-move-to-other-favorite-and-delete-favorite', name: 'item_move_to_other_favorite', methods: ['POST'])]
    public function moveItemToOtherFavoriteAndDeleteFavorite(Request $request): JsonResponse
    {
        $session = $this->requestStack->getSession();
        $session->start();

        if (!$session->has('account') || empty($session->get('account'))) {
            return new JsonResponse('session account is not hydrated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $options = $request->request->all();

        $favoriteId = $options['favoriteId'] ?? null;
        $favoriteIdToReceive= $options['favoriteIdToReceive'] ?? null;
        if (isset($favoriteId, $favoriteIdToReceive)){
            $favorite = $this->favoriteService->moveItemsToOtherFavorite($favoriteId, $favoriteIdToReceive);
            $this->favoriteService->removeFavorite($favoriteId);
            return new JsonResponse($favorite);
        } else {
            throw new Exception('Impossible de supprimer ce favori favoris');
        }
    }
}
