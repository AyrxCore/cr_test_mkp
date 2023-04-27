<?php

namespace App\Repository;

use App\Entity\UpplerProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UpplerProduct>
 *
 * @method UpplerProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method UpplerProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method UpplerProduct[]    findAll()
 * @method UpplerProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UpplerProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UpplerProduct::class);
    }

    public function save(UpplerProduct $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UpplerProduct $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
