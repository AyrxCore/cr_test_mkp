<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\UpplerCartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsController]
class AddOrderItemToCart extends AbstractController
{
    public function __construct(private UpplerCartService $upplerCartService)
    {
    }

    /**
     * @Route("/api/order_items", name="add_order_item_to_cart", methods={"POST"})
     */
    public function __invoke(Request $request, NormalizerInterface $normalizer): JsonResponse
    {
        try {
            $cartId = (int) $request->request->get('cartId');
            $products = $request->request->get('products');
            foreach ($products as $product) {
                $this->upplerCartService->addItemToCart(
                    $cartId,
                    (int) $product['variantId'],
                    (int) $product['quantity'],
                );
            }

            return new JsonResponse(true);
        } catch (\Exception $exception) {
            return new JsonResponse($exception->getMessage());
        }
    }
}
