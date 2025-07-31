<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PartnerStore;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<PartnerStore>
 */
class PartnerStoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PartnerStore::class);
    }

    public function removeByPartnerId(Uuid $partnerId): void
    {
        $this->createQueryBuilder('p')
            ->delete()
            ->where('p.partner = :partnerId')
            ->setParameter('partnerId', $partnerId)
            ->getQuery()
            ->execute();
    }
}
