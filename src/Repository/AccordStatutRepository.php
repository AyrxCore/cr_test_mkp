<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AccordStatut;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccordStatut>
 *
 * @method null|AccordStatut find($id, $lockMode = null, $lockVersion = null)
 * @method null|AccordStatut findOneBy(array $criteria, array $orderBy = null)
 * @method AccordStatut[]    findAll()
 * @method AccordStatut[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccordStatutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccordStatut::class);
    }

    public function save(AccordStatut $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AccordStatut $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
