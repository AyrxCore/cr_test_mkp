<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CartSavings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CartSavings>
 *
 * @method null|CartSavings find($id, $lockMode = null, $lockVersion = null)
 * @method null|CartSavings findOneBy(array $criteria, array $orderBy = null)
 * @method CartSavings[]    findAll()
 * @method CartSavings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartSavingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartSavings::class);
    }

    public function save(CartSavings $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CartSavings $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
