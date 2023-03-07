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
    private const HOME_TOP_VENTE_PROPERTY = [
        'property_id' => '217',
        'value' => '5369'
    ];

    private const HOME_SELECTION_PROPERTY = [
        'property_id' => '217',
        'value' => '5368'
    ];

    private const HOME_ACCORD_CADRE_PROPERTY = [
        'property_id' => '217',
        'value' => '5367'
    ];

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

        $products = $this->upplerProductService->findProductsByOptions($options, ['properties', 'price', 'company', 'images'], $page, $perPage, $showFilters);

        return new JsonResponse($products);
    }

    #[Route('/api/home-products/{type}', name: 'search_home_products', methods: ['GET'])]
    public function homeProduct(Request $request, string $type, NormalizerInterface $normalizer): JsonResponse
    {
        $session= $this->requestStack->getSession();
        $session->start();


        if (!$session->has('account') || empty($session->get('account'))) {
            return new JsonResponse('session account is not hydrated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $options = [];
        $params = ['properties', 'price', 'company', 'images'];

        switch ($type) {
            case 'top-vente':
                $options['properties'] = [self::HOME_TOP_VENTE_PROPERTY];
                break;
            case 'selection':
                $options['properties'] = [self::HOME_SELECTION_PROPERTY];
                break;
            case 'accord-cadre':
                $options['properties'] = [self::HOME_ACCORD_CADRE_PROPERTY];
                $params = ['properties'];
                break;
        }

        $products = $this->upplerProductService->findProductsByOptions($options, $params, self::PAGE, self::PER_PAGE);

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

        $product = $this->upplerProductService->findProductById($id);

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

        $accordCadre = $this->upplerProductService->findProductById($id, ['properties'], (string)$session->get('account')->getId());

        return new JsonResponse($accordCadre);
    }
}
