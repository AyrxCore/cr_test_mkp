<?php

namespace App\Repository;

use App\Entity\LogAccordStatutRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogAccordStatutRequest>
 *
 * @method LogAccordStatutRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogAccordStatutRequest|null findOneBy(array $criteria, array $orderBy = null)
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

//    /**
//     * @return LogAccordStatutRequest[] Returns an array of LogAccordStatutRequest objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LogAccordStatutRequest
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
