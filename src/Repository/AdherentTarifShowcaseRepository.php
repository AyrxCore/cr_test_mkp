<?php

namespace App\Repository;

use App\Entity\AdherentTarifShowcase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdherentTarifShowcase>
 *
 * @method AdherentTarifShowcase|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdherentTarifShowcase|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdherentTarifShowcase[]    findAll()
 * @method AdherentTarifShowcase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdherentTarifShowcaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdherentTarifShowcase::class);
    }
}
