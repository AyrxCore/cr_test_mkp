<?php

namespace App\Controller\api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\Attribute\Required;

#[Route("/api/user")]
class UserApiController extends AbstractController
{
    #[Required]
    public RequestStack $requestStack;

    #[Route('/me', name: 'get_me')]
    public function me(NormalizerInterface $normalizer): JsonResponse
    {
        $session = $this->requestStack->getSession();
        $user = $normalizer->normalize($this->getUser(), 'json', ['groups' => 'simpleUser']);
        // on ajoute les données extraites du token uppler
        $upplerDatas = $session->get('token_datas');
        // on retire les informations qui ne sont pas nécessaires pour le Front
        unset($upplerDatas->exp);
        unset($upplerDatas->userId);
        unset($upplerDatas->companyId);
        unset($upplerDatas->locale);
        $user["upplerDatas"] = $upplerDatas;
        return new JsonResponse($user);
    }

    #[Route("/logout")]
    public function logout()
    {
        $response = new Response();
        $response->headers->clearCookie('BEARER','/');
        return $response;
    }
}
