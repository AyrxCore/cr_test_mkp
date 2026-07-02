<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Dto\CartItem;
use App\Factory\DjustCartFactory;
use App\Service\Account\CurrentAccountProvider;
use App\Service\CartSavingsService;
use App\Service\Djust\DjustCartService;
use App\Service\Djust\ProductFdpSyncService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    private const string SESSION_KEY_CART_REFERENCE = 'djust_cart_reference_for_savings';

    public function __construct(
        private readonly DjustCartService $djustCartService,
        private readonly DjustCartFactory $djustCartFactory,
        private readonly ProductFdpSyncService $productFdpSyncService,
        private readonly CurrentAccountProvider $currentAccountProvider,
        private readonly CartSavingsService $cartSavingsService,
        private readonly RequestStack $requestStack,
    ) {
    }

    #[Route('/api/carts/{cartId}/sync', name: 'post_cart_sync', methods: ['POST'])]
    public function syncCart(string $cartId): JsonResponse
    {
        $removedOfferPriceIds = $this->djustCartService->syncAndCleanCart($cartId);

        return new JsonResponse(['removedOfferPriceIds' => $removedOfferPriceIds]);
    }

    #[Route('/api/carts/{cartId}/lines', name: 'put_cart_product', methods: ['PUT'])]
    public function updateCartProduct(string $cartId, Request $request): JsonResponse
    {
        try {
            $dataCartItems = \json_decode($request->getContent(), true);
            $cartItems = [];

            foreach ($dataCartItems as $item) {
                $cartItem = new CartItem();
                $cartItem->setAction($item['action'])
                    ->setQuantity($item['quantity'])
                    ->setOfferPriceId($item['offerPriceId']);
                $cartItems[] = $cartItem;
            }

            $this->djustCartService->updateCartItems($cartId, $cartItems);

            return new JsonResponse(true);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    #[Route('/api/carts/{cartId}/lines', name: 'delete_cart_lines', methods: ['DELETE'])]
    public function deleteCartLines(string $cartId, Request $request): JsonResponse
    {
        try {
            $body = \json_decode($request->getContent(), true, flags: \JSON_THROW_ON_ERROR);
            $offerPriceIds = $body['offerPriceIds'] ?? [];

            if (empty($offerPriceIds)) {
                throw new BadRequestHttpException('offerPriceIds est requis et ne peut pas être vide.');
            }

            $this->djustCartService->deleteCartLines($cartId, $offerPriceIds);

            return new JsonResponse(true);
        } catch (BadRequestHttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    #[Route('/api/carts/{cartId}/payment-methods', name: 'get_cart_payment_methods', methods: ['GET'])]
    public function getCartPaymentMethods(string $cartId): JsonResponse
    {
        $paymentMethods = $this->djustCartService->getPaymentMethods($cartId);

        return new JsonResponse($paymentMethods);
    }

    #[Route('/api/carts/payments/initiate', name: 'post_cart_payment_initiate', methods: ['POST'])]
    public function initiatePayment(Request $request): JsonResponse
    {
        try {
            $payload = \json_decode($request->getContent(), true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new BadRequestHttpException('Corps de la requête invalide : '.$e->getMessage());
        }

        // Stocker la référence du panier en session pour pouvoir créer CartSavings
        // même dans le cas d'un 3DS où la confirmation arrive via submitPaymentDetails.
        $cartReference = (string) ($payload['reference'] ?? '');
        if ($cartReference !== '') {
            $this->requestStack->getSession()->set(self::SESSION_KEY_CART_REFERENCE, $cartReference);
        }

        $clientIp = $request->getClientIp() ?? '';
        $result = $this->djustCartService->initiatePayment($payload, $clientIp);

        // Créer CartSavings directement si paiement immédiatement autorisé (pas de 3DS redirect).
        // Pour les paiements 3DS, la création se fait dans submitPaymentDetails.
        if ($cartReference !== '' && $this->djustCartService->isPaymentImmediatelyAuthorised($result)) {
            $this->cartSavingsService->createAfterPayment($cartReference, $this->currentAccountProvider->getAccount());
        }

        return new JsonResponse($result);
    }

    #[Route('/api/carts/payments/details', name: 'post_cart_payment_details', methods: ['POST'])]
    public function submitPaymentDetails(Request $request): JsonResponse
    {
        try {
            $payload = \json_decode($request->getContent(), true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new BadRequestHttpException('Corps de la requête invalide : '.$e->getMessage());
        }

        $result = $this->djustCartService->submitPaymentDetails($payload);

        if ($this->djustCartService->isSuccessfulPaymentResult($result)) {
            $cartReference = (string) ($this->requestStack->getSession()->get(self::SESSION_KEY_CART_REFERENCE, '') ?? '');

            if ($cartReference !== '') {
                $this->requestStack->getSession()->remove(self::SESSION_KEY_CART_REFERENCE);
                $this->cartSavingsService->createAfterPayment($cartReference, $this->currentAccountProvider->getAccount());
            }
        }

        return new JsonResponse($result);
    }

    #[Route('/api/carts/{cartId}/product_fdp', name: 'put_cart_product_fdp', methods: ['PUT'])]
    public function syncProductsFdp(string $cartId): JsonResponse
    {
        try {
            $djustCart = $this->djustCartService->getCart();
            $djustCartReference = $djustCart['reference'] ?? null;

            if ($djustCartReference !== $cartId) {
                throw new BadRequestHttpException('Le panier courant ne correspond pas au panier demandé.');
            }

            $cart = $this->djustCartFactory->createFromCommercialOrder($djustCart);
            $this->productFdpSyncService->syncForCart($cartId, $cart);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        return new JsonResponse(true);
    }

    /**
     * Pour chaque ligne de commande des logistic orders du panier courant,
     * injecte le montant de l'éco-participation (PRODUCT_ECOTAXE) dans le CF
     * ORDER_LINE_ECO_TAX afin que les fournisseurs puissent l'afficher sur les factures.
     * Déclenché à la validation du récapitulatif panier (clic "Suivant").
     */
    #[Route('/api/carts/{cartId}/logistic-orders/eco-tax', name: 'post_cart_logistic_orders_eco_tax', methods: ['POST'])]
    public function updateEcoTaxInLogisticOrders(string $cartId): JsonResponse
    {
        $djustCart = $this->djustCartService->getCart();
        $currentCartId = $djustCart['reference'] ?? null;

        if ($currentCartId !== $cartId) {
            throw new BadRequestHttpException('Le panier courant ne correspond pas au panier demandé.');
        }

        $this->djustCartService->updateAllLogisticOrdersLinesEcoTax($djustCart);

        return new JsonResponse(true);
    }

    /**
     * Envoie les informations de l'adhérent connecté (SIRET, email, téléphone)
     * dans les custom fields de chaque logistic order du panier courant.
     * Déclenché à la validation de l'étape Livraison du tunnel de paiement.
     */
    #[Route('/api/carts/{cartId}/logistic-orders/customer-info', name: 'post_cart_logistic_orders_customer_info', methods: ['POST'])]
    public function updateCustomerInfoInLogisticOrders(string $cartId): JsonResponse
    {
        $account = $this->currentAccountProvider->getAccount();

        if ($account === null) {
            throw new BadRequestHttpException('Aucun compte sélectionné.');
        }

        $siret = $account->getAdherent()?->getSiret();
        $email = $account->getUser()?->getEmail();
        $phone = $account->getPhone();

        $djustCart = $this->djustCartService->getCart();
        $currentCartId = $djustCart['reference'] ?? null;

        if ($currentCartId !== $cartId) {
            throw new BadRequestHttpException('Le panier courant ne correspond pas au panier demandé.');
        }

        $this->djustCartService->updateAllLogisticOrdersCustomFields($djustCart, $siret, $email, $phone);

        return new JsonResponse(true);
    }
}
