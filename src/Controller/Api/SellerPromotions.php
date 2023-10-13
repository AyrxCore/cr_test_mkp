<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\UpplerSellerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class SellerPromotions extends AbstractController
{
    public function __construct(private UpplerSellerService $upplerSellerService)
    {
    }

    #[Route('/api/sellers/{id}/promotions', name: 'get_seller_promotion')]
    public function __invoke(int $id): JsonResponse
    {
        $promotion = $this->upplerSellerService->getSellerPromotions($id);

        return new JsonResponse($promotion);
    }
}
