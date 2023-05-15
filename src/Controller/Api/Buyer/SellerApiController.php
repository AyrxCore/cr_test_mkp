<?php

namespace App\Controller\Api\Buyer;

use App\Service\UpplerSellerService;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class SellerApiController extends AbstractController
{
    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UpplerSellerService $upplerSellerService;

    #[Route('/api/sellers', name: 'get_sellers')]
    public function list(NormalizerInterface $normalizer): JsonResponse
    {
        $session = $this->requestStack->getSession();
        $session->start();

        $sellers = $this->upplerSellerService->getSellers(16, 3);

        return new JsonResponse($sellers);
    }

    #[Route('/api/sellers/{id}', name: 'get_seller')]
    public function getSeller(int $id, NormalizerInterface $normalizer): JsonResponse
    {
        $session = $this->requestStack->getSession();
        $session->start();

        $seller = $this->upplerSellerService->getSeller($id);

        return new JsonResponse($seller);
    }

    #[Route('/api/sellers/{id}/promotions', name: 'get_seller_promotion')]
    public function getSellerPromotions(int $id): JsonResponse
    {
        $session = $this->requestStack->getSession();
        $session->start();

        $promotion = $this->upplerSellerService->getSellerPromotions($id);

        return new JsonResponse($promotion);
    }
}
