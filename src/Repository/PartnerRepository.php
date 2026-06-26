<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Partner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Partner>
 */
class PartnerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Partner::class);
    }

    public function findByDjustIds(array $djustIds): array
    {
        if (empty($djustIds)) {
            return [];
        }

        return $this->createQueryBuilder('p')
            ->innerJoin('p.partnerStores', 's')
            ->addSelect('s')
            ->where('p.id IN (:djustIds)')
            ->setParameter('djustIds', $djustIds)
            ->getQuery()
            ->getResult();
    }
}
