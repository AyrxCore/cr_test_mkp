<?php

namespace App\Controller\Api\Buyer;

use App\Service\UpplerAccordCadreService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class AccordCadreApiController extends AbstractController
{
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

        return new JsonResponse($this->upplerAccordCadreService->getAccordsCadresByParams($options, ['properties']));
    }

}
