<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\Favorite;
use App\Entity\UpplerProduct;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Service\Attribute\Required;

class FavoriteService
{
    #[Required]
    public Security $security;

    #[Required]
    public RequestStack $requestStack;

    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function getFavorites(Account $account): array
    {
        return $this->em->getRepository(Favorite::class)->findFavorites($account);
    }

    public function getUpplerProductByProductIdAndVariantId(int $productId, int $variantId, string $name): UpplerProduct
    {
        $product = $this->em->getRepository(UpplerProduct::class)->findOneBy([
            'upplerProductId' => $productId,
            'upplerVariantId' => $variantId,
        ]);
        if (!$product) {
            $product = new UpplerProduct();
            $product->setUpplerProductId($productId);
            $product->setUpplerVariantId($variantId);
            $product->setName($name);

            $this->em->persist($product);
            $this->em->flush();
        }

        return $product;
    }

    public function getUpplerProductById($productId): UpplerProduct
    {
        return $this->em->getRepository(UpplerProduct::class)->find($productId,);
    }

    public function removeFavorite($favoriteId): bool
    {
        try {
            $session = $this->requestStack->getSession();
            $favorite = $this->em->getRepository(Favorite::class)->find($favoriteId);
            if (!$this->security->isGranted('delete', [$favorite, $session->get('account')])) {
                throw new \RuntimeException('Vous n\'êtes pas autorisé à supprimer ce favori.');
            }
            $this->em->remove($favorite);
            $this->em->flush();

            return true;
        } catch (\Exception $exception) {
            throw  new \Exception($exception->getMessage());
        }

    }

    public function moveItemsToOtherFavorite($favoriteId, $favoriteIdToReceive): Favorite
    {
        $favorite = $this->em->getRepository(Favorite::class)->find($favoriteId);
        $favoriteToReceive = $this->em->getRepository(Favorite::class)->find($favoriteIdToReceive);

        foreach ($favorite->getUpplerProducts()->getValues() as $value) {
            $favoriteToReceive->addUpplerProduct($value);
        }
        $this->em->persist($favoriteToReceive);
        $this->em->flush();

        $this->removeFavorite($favoriteId);

        return $favoriteToReceive;
    }

    public function addItemToFavorite($favoriteId, UpplerProduct $product, bool $flush = true)
    {
        $favorite = $this->em->getRepository(Favorite::class)->find($favoriteId);
        $favorite->addUpplerProduct($product);

        $this->em->persist($favorite);

        if ($flush) {
            $this->em->flush();
            return $favorite;
        }
    }

    public function addItemToFavorites(array $favoriteIds, UpplerProduct $product): void
    {
        foreach ($favoriteIds as $favoriteId) {
            $this->addItemToFavorite($favoriteId, $product, false);
        }

        $this->em->flush();
    }

    public function removeItemFromFavorites($favoriteId, $productId): Favorite
    {
        $favorite = $this->em->getRepository(Favorite::class)->find($favoriteId);
        $product = $this->em->getRepository(UpplerProduct::class)->find($productId);
        $favorite->removeUpplerProduct($product);
        $this->em->persist($favorite);
        $this->em->flush();

        return $favorite;
    }
}
