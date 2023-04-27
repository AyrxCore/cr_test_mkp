<?php

namespace App\Controller\Api\Buyer;

use App\Service\UpplerCartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\Attribute\Required;

#[Route('/api/order_items')]
class OrderApiController extends AbstractController
{
    #[Required]
    public UpplerCartService $upplerCartService;

    /**
     * @Route("/multiple", name="add_multiple_order_items_to_cart", methods={"POST"})
     */
    public function __invoke(Request $request, NormalizerInterface $normalizer)
    {
        try {
            $cartId = (int)$request->request->get('cartId');
            $products = $request->request->get('products');
            foreach ($products as $product) {
                $result = $this->upplerCartService->addItemToCart(
                    $cartId,
                    $product['variantId'],
                    $product['quantity'],
                );
            }
            return new JsonResponse(true);
        } catch (\Exception $exception) {
            return new JsonResponse($exception->getMessage());
        }
    }
}
