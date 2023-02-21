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
        $session= $this->requestStack->getSession();
        $session->start();

        if (!$session->has('account') || empty($session->get('account'))) {
            return new JsonResponse('session account is not hydrated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $sellers = $this->upplerSellerService->getSellers(16, 3);

        return new JsonResponse($sellers);
    }

    #[Route('/api/seller/{id}', name: 'get_seller')]
    public function me(int $id, NormalizerInterface $normalizer): JsonResponse
    {
        $session= $this->requestStack->getSession();
        $session->start();

        if (!$session->has('account') || empty($session->get('account'))) {
            return new JsonResponse('session account is not hydrated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $seller = $this->upplerSellerService->getSeller($id);

        return new JsonResponse($seller);
    }
}
