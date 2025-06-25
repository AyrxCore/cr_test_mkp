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

    /**
     * Récupère les partenaires par leurs IDs Uppler.
     */
    public function findByUpplerIds(array $upplerIds): array
    {
        if (empty($upplerIds)) {
            return [];
        }

        return $this->createQueryBuilder('p')
            ->where('p.upplerId IN (:upplerIds)')
            ->setParameter('upplerIds', $upplerIds)
            ->leftJoin('p.partnerStores', 's')
            ->addSelect('s')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les partenaires autorisés avec leurs stores.
     */
    public function findAuthorizedPartnersWithStores(array $upplerIds): array
    {
        if (empty($upplerIds)) {
            return [];
        }

        return $this->createQueryBuilder('p')
            ->innerJoin('p.partnerStores', 's')
            ->addSelect('s')
            ->where('p.upplerId IN (:upplerIds)')
            ->setParameter('upplerIds', $upplerIds)
            ->getQuery()
            ->getResult();
    }
}
