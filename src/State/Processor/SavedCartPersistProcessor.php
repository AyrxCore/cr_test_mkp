<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\SavedCart;
use App\Entity\SavedCartProduct;
use App\Repository\AccountRepository;
use App\Service\UpplerCartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class SavedCartPersistProcessor implements ProcessorInterface
{
    public function __construct(
        private AccountRepository $accountRepository,
        private EntityManagerInterface $em,
        private RequestStack $requestStack,
        private UpplerCartService $upplerCartService,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        try {
            if (($context['operation'] ?? null) instanceof Post) {
                $data = $this->addProductsToCart($data);
            }

            $this->em->persist($data);
            $this->em->flush();

            return $data;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    private function addProductsToCart(SavedCart $savedCart): SavedCart
    {
        $account = $this->requestStack->getSession()->get('account');

        $cart = $this->upplerCartService->getCart();

        if (!$cart) {
            throw new \RuntimeException("Vous n'avez aucun panier en cours.");
        }

        $account = $this->accountRepository->find($account->getId());
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
