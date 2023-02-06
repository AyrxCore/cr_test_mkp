<?php

namespace App\Controller\Api\Buyer;

use App\Service\UpplerProductService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\Attribute\Required;


class ProductApiController extends AbstractController
{
    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UpplerProductService $upplerProductService;

    #[Route('/api/products', name: 'search_products', methods: ['POST'])]
    public function list(Request $request, NormalizerInterface $normalizer): JsonResponse
    {
        $session= $this->requestStack->getSession();
        $session->start();

        $options = $request->request->all();

        if (!$session->has('account') || empty($session->get('account'))) {
            return new JsonResponse('session account is not hydrated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $products = $this->upplerProductService->getProductsByParams($options, ['price', 'properties']);

        return new JsonResponse($products['results']);
    }

}
