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

    private const PAGE = 1;
    private const PER_PAGE = 5;

    #[Route('/api/products', name: 'search_products', methods: ['POST'])]
    #[Route('/api/accords-cadre', name: 'search_accords_cadre', methods: ['POST'])]
    public function list(Request $request, NormalizerInterface $normalizer): JsonResponse
    {
        $session= $this->requestStack->getSession();
        $session->start();

        $options = $request->request->all();

        if (!$session->has('account') || empty($session->get('account'))) {
            return new JsonResponse('session account is not hydrated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $showFilters = false;
        $page = self::PAGE;
        $perPage = self::PER_PAGE;

        if (!empty($options['with_filter'])) {
            $showFilters = true;
            unset($options['with_filter']);
        }

        if (!empty($options['page'])) {
            $page = $options['page'];
            unset($options['page']);
        }

        if (!empty($options['perPage'])) {
            $perPage = $options['perPage'];
            unset($options['perPage']);
        }

        $products = $this->upplerProductService->getProductsByParams($page, $perPage, $options, $showFilters);

        return new JsonResponse($products);
    }

    #[Route('/api/product/{id}', name: 'get_product')]
    public function product(int $id, NormalizerInterface $normalizer): JsonResponse
    {
        $session= $this->requestStack->getSession();
        $session->start();

        if (!$session->has('account') || empty($session->get('account'))) {
            return new JsonResponse('session account is not hydrated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $product = $this->upplerProductService->getProduct($id);

        return new JsonResponse($product);
    }

    #[Route('/api/accord-cadre/{id}', name: 'get_accord_cadre')]
    public function accordCadre(int $id, NormalizerInterface $normalizer): JsonResponse
    {
        $session= $this->requestStack->getSession();
        $session->start();

        if (!$session->has('account') || empty($session->get('account'))) {
            return new JsonResponse('session account is not hydrated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $accordCadre = $this->upplerProductService->getProduct($id, [], (string)$session->get('account')->getId());

        return new JsonResponse($accordCadre);
    }
}
