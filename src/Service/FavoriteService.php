<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\Favorite;
use App\Entity\FavoriteProduct;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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

    /**
     * @throws Exception
     */
    public function createFavorite(Favorite $favorite): bool
    {
        try {
            $session = $this->requestStack->getSession();
            $account = $this->em->getRepository(Account::class)->find($session->get('account')->getId());
            $favorite->setAccount($account);
            $this->em->persist($favorite);
            $this->em->flush();

            return true;
        } catch (Exception $exception) {
            throw  new Exception($exception->getMessage());
        }
    }

    public function updateFavorite(Favorite $favorite): bool
    {
        try {
            $session = $this->requestStack->getSession();
            if (!$this->security->isGranted('edit', [$favorite, $session->get('account')])) {
                throw new \RuntimeException('Vous n\'êtes pas autorisé à modifier ce favori.');
            }
            $this->em->persist($favorite);
            $this->em->flush();
            $this->em->clear();

            return true;
        } catch (Exception $exception) {
            throw  new Exception($exception->getMessage());
        }
    }

    /**
     * @throws Exception
     */
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
            $this->em->clear();

            return true;
        } catch (Exception $exception) {
            throw  new Exception($exception->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function moveItemsToOtherFavorite($favoriteId, $favoriteIdToReceive): Favorite
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

    public function addItemToFavorite($favoriteId, int $productId, int $variantId, string $name, bool $flush = true)
    {
        try {
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
        } catch (Exception $exception) {
            return $exception->getMessage();
        }

    }

    public function addItemToFavorites(array $favoriteIds, int $productId, int $variantId, string $name): void
    {
        foreach ($favoriteIds as $favoriteId) {
            $this->addItemToFavorite($favoriteId, $productId, $variantId, $name, false);
        }

        $this->em->flush();
    }

    public function removeItemFromFavorites($favoriteId, $favoriteProductId): Favorite
    {
        /** @var Favorite $favorite */
        $favorite = $this->em->getRepository(Favorite::class)->find($favoriteId);
        $favoriteProduct = $this->em->getRepository(FavoriteProduct::class)->find($favoriteProductId);
        $this->em->remove($favoriteProduct);
        $this->em->flush();

        return $favorite;
    }

    public function moveItemToFavorite($favoriteId, $favoriteProductId): Favorite
    {
        /** @var Favorite $favorite */
        $favorite = $this->em->getRepository(Favorite::class)->find($favoriteId);
        $favoriteProduct = $this->em->getRepository(FavoriteProduct::class)->find($favoriteProductId);
        $favorite->addFavoriteProduct($favoriteProduct);
        $this->em->persist($favorite);
        $this->em->flush();

        return $favorite;
    }
}
