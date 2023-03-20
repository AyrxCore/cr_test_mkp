<?php

namespace App\Controller\Api\Buyer;

use App\Service\UpplerCartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\Required;

#[Route("/api/buyer/cart")]
class CartApiController extends AbstractController
{
    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UpplerCartService $upplerCartService;

    #[Route('', name: 'get_cart')]
    public function getCartAsBuyer(): JsonResponse
    {
        $session = $this->requestStack->getSession();
        $session->start();

        if (!$session->has('account') || empty($session->get('account'))) {
            return new JsonResponse('session account is not hydrated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $cart = $this->upplerCartService->getCart();

        return new JsonResponse($cart);
    }

    #[Route('/{cartId}/confirm', name: 'confirm_cart')]
    public function confirmCartAsBuyer(int $cartId): Response
    {
        $session = $this->requestStack->getSession();
        $session->start();

        if (!$session->has('account') || empty($session->get('account'))) {
            return $this->redirectToRoute('app');
        }

        $cart = $this->upplerCartService->getCartById($cartId);
        if ($cart['state'] !== 'confirmed') {
            $confirmed = $this->upplerCartService->isPaymentConfirmed($cartId);

            $acceptedStatus = ['processing', 'completed'];

            if ($confirmed && in_array($confirmed->status, $acceptedStatus)) {
                $this->upplerCartService->confirmCart($cartId);
                $this->upplerCartService->processCartSavings($cart);
                return $this->redirect('/app/cart/confirmed');
            }
        }

        return $this->redirect('/app/payment-error');
    }
}
