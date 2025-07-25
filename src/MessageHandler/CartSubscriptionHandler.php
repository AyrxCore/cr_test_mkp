<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Dto\Product;
use App\Message\CartSubscription;
use App\Service\AccordCadreSubscriptionService;
use App\Service\UpplerProductService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CartSubscriptionHandler
{
    public function __construct(private AccordCadreSubscriptionService $accordCadreSubscriptionService, private UpplerProductService $upplerProductService)
    {
    }

    public function __invoke(CartSubscription $message): void
    {
        $productsIds = $message->getProductsIds();

        foreach ($productsIds as $productId) {
            /** @var Product $product */
            $product = $this->upplerProductService->findProductByIdForAdmin($productId);
            $accordId = $this->getAccordId($product['properties']);
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
