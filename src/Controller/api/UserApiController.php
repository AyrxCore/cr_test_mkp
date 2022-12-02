<?php

namespace App\Controller\api;

use App\Entity\User;
use App\Service\UpplerAuthenticationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\Attribute\Required;
use function PHPUnit\Framework\assertGreaterThanOrEqual;

#[Route("/api/user")]
class UserApiController extends AbstractController
{
    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UpplerAuthenticationService $upplerAuthenticationService;

    #[Route('/me', name: 'get_me')]
    public function me(NormalizerInterface $normalizer): JsonResponse
    {
        $session= $this->requestStack->getSession();
        $session->start();

        if (!$session->has('account') || empty($session->get('account'))) {
            return new JsonResponse('session account is not hydrated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $buyerDatas = $this->upplerAuthenticationService->getUserBuyerDatas();
        $user = $normalizer->normalize($this->getUser(), 'json', ['groups' => 'simpleUser']);
        $account = $normalizer->normalize($session->get('account'), 'json', ['groups' => 'simpleUser']);
        $account["buyer"] = $buyerDatas;
        $user["account"] = $account;
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
