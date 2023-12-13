<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SavedCart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SavedCart>
 *
 * @method null|SavedCart find($id, $lockMode = null, $lockVersion = null)
 * @method null|SavedCart findOneBy(array $criteria, array $orderBy = null)
 * @method SavedCart[]    findAll()
 * @method SavedCart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SavedCartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SavedCart::class);
    }

    public function save(SavedCart $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SavedCart $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
