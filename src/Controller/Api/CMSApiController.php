<?php

namespace App\Controller\Api;


use App\Service\UpplerPageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\Required;

#[Route('/api/cms')]
class CMSApiController extends AbstractController
{
    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public UpplerPageService $upplerPageService;

    #[Route('/page/{idPage}', name: 'get_page_by_id')]
    public function page(int $idPage): JsonResponse
    {
        return new JsonResponse($this->upplerPageService->getPageById($idPage));
    }
}
