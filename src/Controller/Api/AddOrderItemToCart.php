<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\UpplerCartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class AddOrderItemToCart extends AbstractController
{
    public function __construct(private readonly UpplerCartService $upplerCartService)
    {
    }

    #[Route(path: '/api/order_items', name: 'add_order_item_to_cart', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = $request->getPayload()->all();
            $cartId = (int) $data['cartId'];
            $products = $data['products'];
            foreach ($products as $product) {
                $this->upplerCartService->addItemToCart(
                    $cartId,
                    (int) $product['variantId'],
                    (int) $product['quantity'],
                );
            }

            return new JsonResponse(true);
        } catch (\Exception $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
    }
}
