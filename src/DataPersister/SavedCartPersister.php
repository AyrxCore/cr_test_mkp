<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Account;
use App\Entity\SavedCart;
use App\Entity\SavedCartProduct;
use App\Service\UpplerCartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SavedCartPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestStack $requestStack,
        private UpplerCartService $upplerCartService,
    ) {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof SavedCart;
    }

    /**
     * @param SavedCart $data
     *
     * @throws \Exception
     */
    public function persist($data, array $context = [])
    {
        try {
            if (($context['collection_operation_name'] ?? null) === 'create') {
                $data = $this->addProductsToCart($data);
            }

            $this->em->persist($data);
            $this->em->flush();

            return $data;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    public function remove($data, array $context = []): bool
    {
        $this->em->remove($data);
        $this->em->flush();

        return true;
    }

    private function addProductsToCart(SavedCart $savedCart): SavedCart
    {
        $account = $this->requestStack->getSession()->get('account');

        $cart = $this->upplerCartService->getCart();

        if (!$cart) {
            throw new \RuntimeException("Vous n'avez aucun panier en cours.");
        }

        $account = $this->em->getRepository(Account::class)->find($account->getId());
        $savedCart->setAccount($account);

        $listItemIds = [];
        foreach ($cart['orders'] as $order) {
            foreach ($order['items'] as $item) {
                $savedCartProduct = new SavedCartProduct();
                $savedCartProduct->setUpplerProductId((int) $item['variant']['product']['id']);
                $savedCartProduct->setUpplerVariantId((int) $item['variant']['id']);
                $savedCartProduct->setUpplerProductName($item['variant']['product']['name']['default']);
                $savedCartProduct->setQuantity((int) $item['quantity']);
                $savedCart->addSavedCartProduct($savedCartProduct);
                $listItemIds[] = $item['id'];
            }
        }

        foreach ($listItemIds as $id) {
            $this->upplerCartService->deleteOrderItem($id);
        }

        return $savedCart;
    }
}
