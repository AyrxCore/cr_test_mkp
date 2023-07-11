<?php

namespace App\Repository;

use App\Entity\Account;
use App\Entity\FavoriteProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FavoriteProduct>
 *
 * @method FavoriteProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method FavoriteProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method FavoriteProduct[]    findAll()
 * @method FavoriteProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FavoriteProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FavoriteProduct::class);
    }

    public function save(FavoriteProduct $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FavoriteProduct $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getFavoritesProductsByAccountAndProductId(Account $account, int $productId): array|float|int|string
    {
        $qb = $this->createQueryBuilder('fp')
            ->select('fp.id');

        $qb
            ->innerJoin('fp.favorite', 'f')
            ->leftJoin('f.account', 'a')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        $qb->expr()->eq('f.account', ':accountId'),
                        $qb->expr()->eq('f.public', ':isPublicFalse')
                    ),
                    $qb->expr()->andX(
                        $qb->expr()->eq('a.adherent', ':adherentId'),
                        $qb->expr()->eq('f.public', ':isPublicTrue')
                    )
                )
            )
            ->andWhere('fp.upplerProductId = :upplerProductId')
            ->setParameter('accountId', $account->getId())
            ->setParameter('isPublicFalse', false)
            ->setParameter('adherentId', $account->getAdherent())
            ->setParameter('isPublicTrue', true)
            ->setParameter('upplerProductId', $productId)
            ->groupBy('fp.id, f.id');

        return $qb
            ->getQuery()
            ->getSingleColumnResult();
    }
}
