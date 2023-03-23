<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\CartSavings;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use stdClass;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

class UpplerCartService extends HttpClientProvider
{
    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UpplerAccountService $upplerAccountService;

    #[Required]
    public AccountRepository $accountRepository;

    protected string $appDomain;

    public function __construct(
        string $env,
        string $apiUrl,
        string $adminClientId,
        string $adminClientSecret,
        string $adminTokenFile,
        string $httpCachePath,
        string $appDomain,
    ) {
        parent::__construct($env, $apiUrl, $adminClientId, $adminClientSecret, $adminTokenFile, $httpCachePath);
        $this->appDomain = $appDomain;
    }

    public function createCart(): string | null
    {
        $res = $this->request(
            'POST',
            $this->apiUrl . 'v1/buyer/cart/',
        );

        if (Response::HTTP_CREATED === $res->getStatusCode()) {
            $headers = $res->getHeaders();
            return $headers['location'][0];
        }

        return null;
    }

    public function getCart(): array | null
    {
        $res = $this->request(
            'GET',
            $this->apiUrl . 'v1/buyer/cart?&expand[]=orders&expand[]=orderItems&criteria[state][]=new&criteria[state][]=address&criteria[state][]=shipping_method&criteria[state][]=payment',
        );


        if (Response::HTTP_OK === $res->getStatusCode()) {
            $carts = json_decode($res->getContent(), true);

            if (sizeof($carts) > 0) {
                $cart = reset($carts);

                if (is_null($cart['shipping_address']) || is_null($cart['billing_address'])) {
                    $account = $this->upplerAccountService->getUserSubAccountDatas();
                    $this->updateCartAddress($cart['id'], $account->shipping_address, $account->billing_address);
                }
                if (sizeof($cart['orders']) > 0) {

                    $cart['paymentMethods'] = $this->getPaymentMethods($cart['id']);

                    $res = $this->request(
                        'GET',
                        $this->apiUrl . 'v1/buyer/cart/' . $cart['id'] . '/shipping-method',
                    );
                    $shippingMethods = json_decode($res->getContent());
                    foreach ($cart['orders'] as $keyOrder => $order) {
                        $shippingMethodsAvailable = $this->getShippingMethodsForOrder(
                            $shippingMethods->shipment_proposal->method_proposals,
                            $order['id']
                        );
                        $cart['orders'][$keyOrder]['shippingMethodsAvailable'] = $shippingMethodsAvailable;

                        if (sizeof($order['shipments']) === 0 && sizeof($shippingMethodsAvailable) > 0) {
                            $this->setShippingMethod($cart['id'], $order['id'], $shippingMethodsAvailable[0]->shipping_method->id);
                            return $this->getCart();
                        }
                    }
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

    public function getCartById(int $cartId): array | null
    {
        $res = $this->request(
            'GET',
            $this->apiUrl . 'v1/buyer/cart/' . $cartId . '?expand[]=orders&expand[]=orderItems',
        );

        if (Response::HTTP_OK === $res->getStatusCode()) {
            return json_decode($res->getContent(), true);
        }
        return null;
    }

    public function addItemToCart(int $cartId, int $variantId, int $quantity): bool
    {
        $res = $this->request(
            'POST',
            $this->apiUrl . 'v1/buyer/cart/' . $cartId . '/items',
            [
                'json' => [
                    'items' => [
                        [
                            'variant_id' => $variantId,
                            'quantity' => $quantity,
                        ]
                    ]
                ],
            ],
        );
        return $res && Response::HTTP_NO_CONTENT === $res->getStatusCode();
    }

    public function updateCartAddress(int|string $cartReference, int $shippingId, int $billingId): bool
    {
        $res = $this->request(
            'PATCH',
            is_string($cartReference) ? $cartReference : $this->apiUrl . 'v1/buyer/cart/' . $cartReference,
            [
                'json' => [
                    'shipping_address' => $shippingId,
                    'billing_address' => $billingId,
                ],
            ],
        );
        return $res && Response::HTTP_NO_CONTENT === $res->getStatusCode();
    }

    public function updateOrderItemQuantity(int $id, int $quantity): bool
    {
        $res = $this->request(
            'PATCH',
            $this->apiUrl . 'v1/buyer/order-item/' . $id,
            [
                'json' => ['quantity' => $quantity],
            ],
        );
        return $res && Response::HTTP_NO_CONTENT === $res->getStatusCode();
    }

    public function deleteOrderItem(int $id): bool
    {
        $res = $this->request(
            'DELETE',
            $this->apiUrl . 'v1/buyer/order-item/' . $id,
        );
        return $res && Response::HTTP_NO_CONTENT === $res->getStatusCode();
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
            $this->apiUrl . 'v1/buyer/cart/' . $cartId . '/shipping-method',
            [
                'json' => [
                    'shipment_proposal' => [
                        'method_proposals' => [
                            [
                                'order' => $orderId,
                                'shipping_method' => $shippingMethodId,
                            ],
                        ]
                    ],
                ],
            ],
        );
        return $res && Response::HTTP_NO_CONTENT === $res->getStatusCode();
    }

    public function getPaymentMethods(int $cartId): array
    {
        $res = $this->request(
            'GET',
            $this->apiUrl . 'v1/buyer/cart/' . $cartId . '/payment-method',
        );
        if ($res && Response::HTTP_OK === $res->getStatusCode()) {
            return json_decode($res->getContent());
        }
        return [];
    }

    public function setPaymentMethod(int $cartId, int $paymentMethodId): stdClass
    {
        $res = $this->request(
            'PATCH',
            $this->apiUrl . 'v1/buyer/cart/' . $cartId . '/payment-method',
            [
                'json' => [
                    'payment_method' => $paymentMethodId,
                    'callback_url' => $this->appDomain . '/api/buyer/cart/' . $cartId . '/confirm',
                ],
            ],
        );
        if ($res && Response::HTTP_OK === $res->getStatusCode()) {
            return json_decode($res->getContent());
        }
        return false;
    }

    public function isPaymentConfirmed(int $cartId): stdClass | bool
    {
        $res = $this->request(
            'GET',
            $this->apiUrl . 'v1/buyer/cart/' . $cartId . '/transaction/confirm',
        );
        if ($res && Response::HTTP_OK === $res->getStatusCode()) {
            return json_decode($res->getContent());
        }
        return false;
    }

    public function confirmCart(int $cartId): bool
    {
        $res = $this->request(
            'PATCH',
            $this->apiUrl . 'v1/buyer/cart/' . $cartId . '/confirm',
        );

        return $res && Response::HTTP_NO_CONTENT === $res->getStatusCode();
    }

    public function processCartSavings(array $cart): void
    {
        $account = $this->accountRepository->findOneBy([
            'upplerUserId' => $cart['user']['id']
        ]);

        foreach ($cart['orders'] as $order) {
            $cartSaving = new CartSavings();
            $cartSaving->setCartId($cart['id']);
            $cartSaving->setAccount($account);
            $cartSaving->setOrderId($order['id']);
            $cartSaving->setSellerId($order['seller']['id']);
            $priceReferenceByOrder = 0;
            foreach ($order['items'] as $item) {
                $pricePaid = $item['variant']['product']['price_reference'] * $item['quantity'];

                // Si le price_reference = null alors pricePaid = 0
                if ($pricePaid === 0) {
                    $pricePaid = $item['total_excluding_taxes'];
                }

                $priceReferenceByOrder += $pricePaid;
            }
            $cartSaving->setAmount($priceReferenceByOrder - $order['items_total_excluding_taxes']);
            $this->em->persist($cartSaving);
        }

        $this->em->flush();
    }
}
