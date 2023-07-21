<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UpplerCartService extends AbstractUpplerService
{
    public function __construct(
        HttpClientInterface $upplerClient,
        RequestStack $requestStack,
        string $upplerEnv,
        string $adminClientId,
        string $adminClientSecret,
        string $adminTokenFile,
        string $httpCachePath,
        private string $appDomain,
    ) {
        parent::__construct(
            upplerClient: $upplerClient,
            requestStack: $requestStack,
            upplerEnv: $upplerEnv,
            adminClientId: $adminClientId,
            adminClientSecret: $adminClientSecret,
            adminTokenFile: $adminTokenFile,
            httpCachePath: $httpCachePath,
        );
    }

    public function createCart(): string|null
    {
        $res = $this->request(
            'POST',
            'v1/buyer/cart/',
        );

        if ($res->getStatusCode() === Response::HTTP_CREATED) {
            $headers = $res->getHeaders();

            return $headers['location'][0];
        }

        return null;
    }

    public function getCart(): array|null
    {
        $res = $this->request(
            'GET',
            'v1/buyer/cart?&expand[]=orders&expand[]=orderItems&criteria[state][]=new&criteria[state][]=address&criteria[state][]=shipping_method&criteria[state][]=payment&sorting[id]=asc&perPage=1&page=1',
        );

        if (\in_array($res->getStatusCode(), [Response::HTTP_OK, Response::HTTP_PARTIAL_CONTENT], true)) {
            $carts = \json_decode($res->getContent(), true);

            if (\count($carts) > 0) {
                $cart = $carts[0];

                if ($cart['state'] === 'payment') {
                    $this->isPaymentConfirmed($cart['id']);
                }
                if (\count($cart['orders']) > 0) {
                    $cart['paymentMethods'] = $this->getPaymentMethods($cart['id']);
                }

                return $cart;
            } else {
                if ($this->createCart()) {
                    return $this->getCart();
                }
            }
        }

        return null;
    }

    public function getCartById(int $cartId): array|null
    {
        $res = $this->request(
            'GET',
            'v1/buyer/cart/'.$cartId.'?expand[]=orders&expand[]=orderItems',
        );

        if ($res->getStatusCode() === Response::HTTP_OK) {
            return \json_decode($res->getContent(), true);
        }

        return null;
    }

    public function addItemToCart(int $cartId, int $variantId, int $quantity): bool
    {
        $res = $this->request(
            'POST',
            'v1/buyer/cart/'.$cartId.'/items',
            [
                'json' => [
                    'items' => [
                        [
                            'variant_id' => $variantId,
                            'quantity' => $quantity,
                        ],
                    ],
                ],
            ],
        );

        return $res && $res->getStatusCode() === Response::HTTP_NO_CONTENT;
    }

    public function updateCartAddress(int|string $cartReference, int $shippingId, int $billingId): bool
    {
        $res = $this->request(
            'PATCH',
            \is_string($cartReference) ? $cartReference : 'v1/buyer/cart/'.$cartReference,
            [
                'json' => [
                    'shipping_address' => $shippingId,
                    'billing_address' => $billingId,
                ],
            ],
        );

        return $res && $res->getStatusCode() === Response::HTTP_NO_CONTENT;
    }

    public function updateOrderItemQuantity(int $id, int $quantity): bool
    {
        $res = $this->request(
            'PATCH',
            'v1/buyer/order-item/'.$id,
            [
                'json' => ['quantity' => $quantity],
            ],
        );

        return $res && $res->getStatusCode() === Response::HTTP_NO_CONTENT;
    }

    public function deleteOrderItem(int $id): bool
    {
        $res = $this->request(
            'DELETE',
            'v1/buyer/order-item/'.$id,
        );

        return $res && $res->getStatusCode() === Response::HTTP_NO_CONTENT;
    }

    public function getShippingMethods(int $cartId): array|bool
    {
        $res = $this->request(
            'GET',
            'v1/buyer/cart/'.$cartId.'/shipping-method',
        );
        if ($res && $res->getStatusCode() === Response::HTTP_OK) {
            $res = \json_decode($res->getContent(), true);

            return $res['shipment_proposal']['method_proposals'];
        }

        return false;
    }

    public function getShippingMethodsForOrder(array $shippingMethods, int $orderId): array
    {
        $methods = [];
        foreach ($shippingMethods as $method) {
            if ($method->order->id === $orderId) {
                $methods[] = $method;
            }
        }

        return $methods;
    }

    public function setShippingMethod(int $cartId, int $orderId, int $shippingMethodId): bool
    {
        $res = $this->request(
            'PATCH',
            'v1/buyer/cart/'.$cartId.'/shipping-method',
            [
                'json' => [
                    'shipment_proposal' => [
                        'method_proposals' => [
                            [
                                'order' => $orderId,
                                'shipping_method' => $shippingMethodId,
                            ],
                        ],
                    ],
                ],
            ],
        );

        return $res && $res->getStatusCode() === Response::HTTP_NO_CONTENT;
    }

    public function getPaymentMethods(int $cartId): array
    {
        $res = $this->request(
            'GET',
            'v1/buyer/cart/'.$cartId.'/payment-method',
        );
        if ($res && $res->getStatusCode() === Response::HTTP_OK) {
            return \json_decode($res->getContent());
        }

        return [];
    }

    public function setPaymentMethod(int $cartId, int $paymentMethodId): array|bool
    {
        $res = $this->request(
            'PATCH',
            'v1/buyer/cart/'.$cartId.'/payment-method',
            [
                'json' => [
                    'payment_method' => $paymentMethodId,
                    'callback_url' => $this->appDomain.'/api/buyer/cart/'.$cartId.'/confirm',
                ],
            ],
        );
        if ($res && $res->getStatusCode() === Response::HTTP_OK) {
            return \json_decode($res->getContent(), true);
        }

        return false;
    }

    public function isPaymentConfirmed(int $cartId): array|bool
    {
        $res = $this->request(
            'GET',
            'v1/buyer/cart/'.$cartId.'/transaction/confirm',
        );
        if ($res && $res->getStatusCode() === Response::HTTP_OK) {
            return \json_decode($res->getContent(), true);
        }

        return false;
    }

    public function confirmCart(int $cartId): bool
    {
        $res = $this->request(
            'PATCH',
            'v1/buyer/cart/'.$cartId.'/confirm',
        );

        return $res && $res->getStatusCode() === Response::HTTP_NO_CONTENT;
    }
}
