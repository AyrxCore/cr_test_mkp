<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\SavedCart;
use App\Entity\SavedCartProduct;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class SavedCartService
{
    #[Required]
    public Security $security;

    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public UpplerCartService $upplerCartService;

    #[Required]
    public NormalizerInterface $normalizer;

    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function getSavedCarts(Account $account): array
    {
        $savedCarts = $this->em->getRepository(SavedCart::class)->findBy(['account' => $account->getId()]);
        return $this->normalizer->normalize($savedCarts, 'json', ['groups' => 'savedCart:get']);
    }

    /**
     * @throws Exception
     */
    public function create(SavedCart $savedCart): SavedCart
    {
        try {
            $session = $this->requestStack->getSession();
            $cart = $this->upplerCartService->getCart();
            if ($cart) {
                $account = $this->em->getRepository(Account::class)->find($session->get('account')->getId());
                $savedCart->setAccount($account);
                $listItemIds = [];
                foreach ($cart['orders'] as $order) {
                    foreach ($order['items'] as $item) {
                        $savedCartProduct = new SavedCartProduct();
                        $savedCartProduct->setUpplerProductId((int)$item['variant']['product']['id']);
                        $savedCartProduct->setUpplerVariantId((int)$item['variant']['id']);
                        $savedCartProduct->setUpplerProductName($item['variant']['product']['name']['default']);
                        $savedCartProduct->setQuantity((int)$item['quantity']);
                        $savedCart->addSavedCartProduct($savedCartProduct);
                        $listItemIds[] = $item['id'];
                    }
                }

                $this->em->persist($savedCart);
                $this->em->flush();
                $this->em->clear();
                foreach ($listItemIds as $id) {
                    $this->upplerCartService->deleteOrderItem($id);
                }
            } else {
                throw new \RuntimeException('Vous n\'avaez aucun panier en cours.');
            }
            return $savedCart;
        } catch (Exception $exception) {
            throw  new Exception($exception->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function update(SavedCart $savedCart): bool
    {
        try {
            $session = $this->requestStack->getSession();
            if (!$this->security->isGranted('edit', [$savedCart, $session->get('account')])) {
                throw new \RuntimeException('Vous n\'êtes pas autorisé à modifier ce panier.');
            }

            $this->em->persist($savedCart);
            $this->em->flush();

            return true;
        } catch (Exception $exception) {
            throw  new Exception($exception->getMessage());
        }

    }

    /**
     * @throws Exception
     */
    public function removeSavedCart(SavedCart $savedCart): bool
    {
        try {
            $session = $this->requestStack->getSession();
            if (!$this->security->isGranted('delete', [$savedCart, $session->get('account')])) {
                throw new \RuntimeException('Vous n\'êtes pas autorisé à supprimer ce panier.');
            }
            $this->em->remove($savedCart);
            $this->em->flush();

            return true;
        } catch (Exception $exception) {
            throw  new Exception($exception->getMessage());
        }

    }
}
