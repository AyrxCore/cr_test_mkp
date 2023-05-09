<?php
namespace App\Controller\Api\Buyer;

use App\Entity\SavedCart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route("/api/saved-carts")]
class SavedCartApiController extends AbstractController
{
    /**
     * @Route("/{id}/products", name="get_saved_cart_products", methods={"GET"})
     */
    public function __invoke(SavedCart $savedCart, NormalizerInterface $normalizer)
    {
        $jsonSavedCart = $normalizer->normalize($savedCart, 'json', ['groups' => 'savedCart:get']);

        return new JsonResponse($jsonSavedCart);
    }
}
