<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\LogAccordStatutRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogAccordStatutRequest>
 *
 * @method null|LogAccordStatutRequest find($id, $lockMode = null, $lockVersion = null)
 * @method null|LogAccordStatutRequest findOneBy(array $criteria, array $orderBy = null)
 * @method LogAccordStatutRequest[]    findAll()
 * @method LogAccordStatutRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogAccordStatutRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogAccordStatutRequest::class);
    }

    public function save(LogAccordStatutRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LogAccordStatutRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
