<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\CartSavings;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class CartService
{
    #[Required]
    public AccountRepository $accountRepository;

    #[Required]
    public EntityManagerInterface $em;

    public function __construct(
    ) {
    }

    public function processCartSavings(array $cart): void
    {
        $account = $this->accountRepository->findOneBy([
            'upplerUserId' => $cart['user']['id'],
        ]);

        if (!$account) {
            throw new \Exception('Account not found for cart savings');
        }

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
