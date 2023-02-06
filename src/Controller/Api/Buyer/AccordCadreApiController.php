<?php

namespace App\Controller\Api\Buyer;

use App\Service\UpplerAccordCadreService;
use App\Service\UpplerProductService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class AccordCadreApiController extends AbstractController
{

    public function __construct(private AdapterInterface $cache)
    {
    }

    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public EntityManagerInterface $em;
    #[Required]
    public UpplerAccordCadreService $upplerAccordCadreService;

    #[Route('/api/accords-cadre', name: 'search_accords_cadre', methods: ['POST'])]
    public function list(Request $request, NormalizerInterface $normalizer): JsonResponse
    {
        $session= $this->requestStack->getSession();
        $session->start();


        $options = $request->request->all();

        if (!$session->has('account') || empty($session->get('account'))) {
            return new JsonResponse('session account is not hydrated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $this->cache->clear('liste_accords_cadre');
        $item = $this->cache->getItem('liste_accords_cadre');
        if (!$item->isHit()) {
            $accordsCadre = $this->upplerAccordCadreService->getAccordsCadresByParams($options, ['properties']);
            $item->set($accordsCadre);
            $item->expiresAfter(new \DateInterval('P1D')); // the item will be cached for 10 seconds
            $this->cache->save($item);

        }

        return new JsonResponse($item->get());
    }

}
