<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Favorite;
use App\Entity\FavoriteProduct;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
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

    public function addProductToFavorite($favoriteId, int $productId, int $variantId, string $name, bool $flush = true)
    {
        try {
            if ($this->getFavoriteProduct($favoriteId, $variantId)) {
                return null;
            }
            /** @var Favorite $favorite */
            $favorite = $this->em->getRepository(Favorite::class)->find($favoriteId);
            $favoriteProduct = new FavoriteProduct();
            $favoriteProduct->setUpplerProductId($productId);
            $favoriteProduct->setUpplerVariantId($variantId);
            $favoriteProduct->setUpplerProductName($name);
            $favorite->addFavoriteProduct($favoriteProduct);

            $this->em->persist($favorite);

            if ($flush) {
                $this->em->flush();

                return $favorite;
            }
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function addProductToFavorites(array $favoriteIds, int $productId, int $variantId, string $name): void
    {
        $account = $this->requestStack->getSession()->get('account');

        $oldSelectedFavorites = $this->em->getRepository(FavoriteProduct::class)->getFavoritesProductsByAccountAndProductId($account, $productId);

        $favoritesIdToRemove = \array_diff($oldSelectedFavorites, $favoriteIds);
        $favoritesIdToAdd = \array_diff($favoriteIds, $oldSelectedFavorites);

        foreach ($favoritesIdToRemove as $favoriteProductId) {
            $this->removeProductFromFavorites($favoriteProductId);
        }

        foreach ($favoritesIdToAdd as $favoriteId) {
            $this->addProductToFavorite($favoriteId, $productId, $variantId, $name, false);
        }

        $this->em->flush();
    }

    /**
     * @throws \Exception
     */
    public function moveProductToFavorite(string $favoriteId, string $favoriteProductId): ?Favorite
    {
        /** @var Favorite $favorite */
        $favorite = $this->em->getRepository(Favorite::class)->find($favoriteId);

        if ($favorite === null) {
            return null;
        }

        /** @var Favorite $favorite */
        $favorite = $this->em->getRepository(Favorite::class)->find($favoriteId);
        $favoriteProduct = $this->em->getRepository(FavoriteProduct::class)->find($favoriteProductId);
        $favorite->addFavoriteProduct($favoriteProduct);
        $this->em->persist($favorite);
        $this->em->flush();

        return $favorite;
    }

    /**
     * Permet de faire la suppression d'une liste de favori tout en déplaçant tous les produits de la liste à supprimer vers une nouvelle liste de favori existante.
     *
     * @throws \Exception
     */
    public function moveProductsToOtherFavorite($favoriteId, $favoriteIdToReceive): Favorite
    {
        /** @var Favorite $favorite */
        $favorite = $this->em->getRepository(Favorite::class)->find($favoriteId);
        /** @var Favorite $favoriteToReceive */
        $favoriteToReceive = $this->em->getRepository(Favorite::class)->find($favoriteIdToReceive);
        foreach ($favorite->getFavoriteProducts()->getValues() as $value) {
            $favoriteToReceive->addFavoriteProduct($value);
        }

        $this->em->persist($favoriteToReceive);
        $this->em->flush();
        $this->em->clear();

        return $favoriteToReceive;
    }

    public function removeProductFromFavorites(string $favoriteProductId): bool
    {
        $favoriteProduct = $this->em->getRepository(FavoriteProduct::class)->find($favoriteProductId);
        $this->em->remove($favoriteProduct);
        $this->em->flush();

        return true;
    }

    private function getFavoriteProduct($favoriteId, $variantId): ?FavoriteProduct
    {
        return $this->em->getRepository(FavoriteProduct::class)->findOneBy([
            'favorite' => $favoriteId,
            'upplerVariantId' => $variantId,
        ]);
    }
}
