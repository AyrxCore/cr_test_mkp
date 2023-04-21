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

#[Route('/api/buyer/cart')]
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

        $cart = $this->upplerCartService->getCart();

        return new JsonResponse($cart);
    }

    #[Route('/{cartId}/confirm', name: 'confirm_cart')]
    public function confirmCartAsBuyer(int $cartId): Response
    {
        $session = $this->requestStack->getSession();
        $session->start();

        if (
            !$session->has('account') || empty($session->get('account'))
            || !$session->has('access_token') || empty($session->get('access_token'))
        ) {
            return $this->redirectToRoute('prehome');
        }

        $cart = $this->upplerCartService->getCartById($cartId);
        if ($cart['state'] !== 'confirmed') {
            $confirmed = $this->upplerCartService->isPaymentConfirmed($cartId);

            $acceptedStatus = ['processing', 'completed'];

            if ($confirmed && in_array($confirmed->status, $acceptedStatus)) {
                $this->upplerCartService->confirmCart($cartId);
                $this->upplerCartService->processCartSavings($cart);

                return $this->redirect('/cart/confirmed/' . $cartId);
            }
        }

        return $this->redirect('/cart/payment-error');
    }

    #[Route('/{cartId}', name: 'get_cart_by_id')]
    public function getCartById(int $cartId): Response
    {
        $session = $this->requestStack->getSession();
        $session->start();

        $cartResume = new \stdClass();
        $cartResume->cart = $this->upplerCartService->getCartById($cartId);
        // $cartResume->confirmation = $this->upplerCartService->isPaymentConfirmed($cartId);

        return new JsonResponse($cartResume);
    }
}
