<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SavedCartProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SavedCartProduct>
 *
 * @method null|SavedCartProduct find($id, $lockMode = null, $lockVersion = null)
 * @method null|SavedCartProduct findOneBy(array $criteria, array $orderBy = null)
 * @method SavedCartProduct[]    findAll()
 * @method SavedCartProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SavedCartProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SavedCartProduct::class);
    }

    public function save(SavedCartProduct $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SavedCartProduct $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
