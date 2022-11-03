<?php

namespace App\Controller\api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route("/api/user")]
class UserApiController extends AbstractController
{
    #[Route('/me', name: 'get_me')]
    public function me(NormalizerInterface $normalizer): JsonResponse
    {
        $user = $normalizer->normalize($this->getUser(), 'json', ['groups' => 'simpleUser']);
        return $this->json($user);
    }
}
