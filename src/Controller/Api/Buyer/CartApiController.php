<?php

declare(strict_types=1);

namespace App\Controller\Api\Buyer;

use App\Dto\CartPayment;
use App\Service\CartService;
use App\Service\UpplerCartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
    public CartService $cartService;

    #[Required]
    public UpplerCartService $upplerCartService;

    #[Route('', name: 'get_cart')]
    public function getCartAsBuyer(): JsonResponse
    {
        $cart = $this->upplerCartService->getCart();

        return new JsonResponse($cart);
    }

    #[Route('/{cartId}/confirm', name: 'confirm_cart')]
    public function confirmCartAsBuyer(int $cartId): Response
    {
        $cart = $this->upplerCartService->getCartById($cartId);
        if ($cart !== null && $cart['state'] !== 'confirmed') {
            $confirmed = $this->upplerCartService->isPaymentConfirmed($cartId);

            $acceptedStatus = ['completed'];

            $paymentMethod = $cart['payment_method'];
            if (isset($paymentMethod) && $paymentMethod['id'] === CartPayment::CART_PAYMENT_MANDAT_ADMIN) {
                $acceptedStatus[] = 'new';
            }

            if ($confirmed && \in_array($confirmed['status'], $acceptedStatus, true)) {
                $this->upplerCartService->confirmCart($cartId);
                $this->cartService->processCartSavings($cart);

                return $this->redirect('/cart/confirmed/'.$cartId);
            }
        }

        return $this->redirect('/cart/payment-error');
    }

    #[Route('/{cartId}/shipments', name: 'get_cart_shipping_methods')]
    public function getCartShippingMethods(int $cartId): Response
    {
        $shippingMethods = $this->upplerCartService->getShippingMethods($cartId);

        if (!$shippingMethods) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse($shippingMethods);
    }

    #[Route('/{cartId}', name: 'get_cart_by_id')]
    public function getCartById(int $cartId): Response
    {
        $cartResume = new \stdClass();
        $cartResume->cart = $this->upplerCartService->getCartById($cartId);

        if (!$cartResume->cart) {
            throw new NotFoundHttpException();
        }
        // $cartResume->confirmation = $this->upplerCartService->isPaymentConfirmed($cartId);

        return new JsonResponse($cartResume);
    }
}
