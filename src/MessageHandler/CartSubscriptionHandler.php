<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Dto\Product;
use App\Message\CartSubscription;
use App\Service\AccordCadreSubscriptionService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CartSubscriptionHandler
{
    public function __construct(private AccordCadreSubscriptionService $accordCadreSubscriptionService)
    {
    }

    public function __invoke(CartSubscription $message): void
    {
        $productsIds = $message->getProductsIds();

        foreach ($productsIds as $productId) {
            //            TODO: Décommenter et adapter le rattachement automatique (récup des produits du panier et de l'accountId) pour créer un message CartSubscription)
            /** @var Product $product */
            //            $product = $this->upplerProductService->findProductByIdForAdmin($productId);
            //            $accordId = $this->getAccordId($product['properties']);
            $accordId = null;
            if ($accordId) {
                $params = [
                    'accordId' => $accordId,
                ];
                $this->accordCadreSubscriptionService->subscription($params, $message->getAccountId(), $message->getChannel(), isSendEmail: false);
            }
        }
    }

    private function getAccordId($properties): ?string
    {
        foreach ($properties as $property) {
            if ($property['property']['name']['default'] === 'accord-id') {
                return $property['value'];
            }
        }

        return null;
    }
}
