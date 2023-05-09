<?php

namespace App\Repository;

use App\Entity\Account;
use App\Entity\Favorite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Favorite>
 *
 * @method Favorite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Favorite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Favorite[]    findAll()
 * @method Favorite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FavoriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Favorite::class);
    }

    public function save(Favorite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Favorite $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Favorite[] Returns an array of favorite objects
     */
    public function findFavorites(Account $account): array
    {
        $qb =  $this->createQueryBuilder('f')
        ->select('f.id AS id, f.name AS name, f.createdAt AS createdAt, f.updatedAt AS updatedAt, a.id as accountId, f.public AS public, COUNT(fp.id) AS nbFavoriteProducts');

        $qb
            ->innerJoin('f.account', 'a')
            ->leftJoin('f.favoriteProducts', 'fp')
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
            ->setParameter('accountId', $account->getId())
            ->setParameter('isPublicFalse', false)
            ->setParameter('adherentId', $account->getAdherent())
            ->setParameter('isPublicTrue', true)
            ->groupBy('f.id, a.id')
            ->orderBy('f.name', 'ASC');

        return $qb
            ->getQuery()
            ->getScalarResult()
        ;
    }

    public function getFavoritesByAccountAndProducId(Account $account, int $productId): array|float|int|string
    {
        $qb =  $this->createQueryBuilder('f')
            ->select('up.id, f.id');

        $qb
            ->innerJoin('f.account', 'a')
            ->leftJoin('f.upplerProducts', 'up')
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
            ->andWhere('up.upplerProductId = :upplerProductId')
            ->setParameter('accountId', $account->getId())
            ->setParameter('isPublicFalse', false)
            ->setParameter('adherentId', $account->getAdherent())
            ->setParameter('isPublicTrue', true)
            ->setParameter('upplerProductId', $productId)
            ->groupBy('up.id, f.id');

        return $qb
            ->getQuery()
            ->getScalarResult()
            ;
    }
}
