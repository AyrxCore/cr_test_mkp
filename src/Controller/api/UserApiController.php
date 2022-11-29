<?php

namespace App\Controller\api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\Attribute\Required;

#[Route("/api/user")]
class UserApiController extends AbstractController
{
    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public EntityManagerInterface $em;

    #[Route('/me', name: 'get_me')]
    public function me(NormalizerInterface $normalizer): JsonResponse
    {
        $user = $normalizer->normalize($this->getUser(), 'json', ['groups' => 'simpleUser']);
        return new JsonResponse($user);
    }

    #[Route("/logout")]
    public function logout()
    {
        $response = new Response();
        $response->headers->clearCookie('BEARER','/');
        return $response;
    }

    #[Route("/find", methods: ['POST'])]
    public function find(Request $request, RateLimiterFactory $protectedRateApiLimiter)
    {
        //ce endpoint est public, pas le choix, donc on limite le nbre d'appel pour éviter les attaques
        $limiter = $protectedRateApiLimiter->create($request->getClientIp());

        if (false === $limiter->consume(1)->isAccepted()) {
            throw new TooManyRequestsHttpException();
        }

        if ($request->request->has('email')) {
            $email = $request->request->get('email');
            $check = $this->em->getRepository(User::class)->findOneBy(['username' => $email]);
            return new JsonResponse(['exist' => null !== $check]);
        }

        throw  new \JsonException('email attribute missing');
    }
}
