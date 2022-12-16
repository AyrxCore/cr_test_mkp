<?php

namespace App\Controller\Api\Buyer;

use App\Service\UpplerCompanyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\Attribute\Required;

#[Route("/api/buyer/company")]
class CompanyApiController extends AbstractController
{
    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UpplerCompanyService $upplerCompanyService;

    #[Route('/adresses', name: 'get_adresses')]
    public function me(NormalizerInterface $normalizer): JsonResponse
    {
        $session= $this->requestStack->getSession();
        $session->start();

        if (!$session->has('account') || empty($session->get('account'))) {
            return new JsonResponse('session account is not hydrated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $addresses = $this->upplerCompanyService->getAdresses();

        return new JsonResponse($addresses);
    }

}
