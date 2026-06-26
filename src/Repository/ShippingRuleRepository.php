<?php

namespace App\Repository;

use App\Entity\ShippingRule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShippingRule>
 */
class ShippingRuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShippingRule::class);
    }

    public function findByPartnerAndType(string $partnerId, string $type): ?ShippingRule
    {
        return $this->createQueryBuilder('sr')
            ->join('sr.partner', 'p')
            ->where('p.id = :partnerId')
            ->andWhere('sr.type = :type')
            ->setParameter('partnerId', $partnerId)
            ->setParameter('type', $type)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
