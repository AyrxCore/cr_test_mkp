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
    /**
     * Longueur minimale d'un orderId Djust (paddé à 10 caractères, ex: "0000625247").
     * Permet d'exclure les anciens orderId Uppler numériques courts non synchronisables via l'API Djust.
     */
    private const int DJUST_ORDER_ID_MIN_LENGTH = 4;

    private const array TERMINAL_ORDER_STATES = [
        'COMPLETED', 'DELIVERED', 'RETURNED',
        'DECLINED_BY_CUSTOMER', 'DECLINED_BY_SUPPLIER', 'DECLINED', 'EXPIRED',
        'CANCELED', 'CANCELLED', 'PARTIALLY_CANCELED', 'REFUSED',
    ];

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

    /**
     * @return iterable<CartSavings>
     */
    public function iterateWithOrderId(): iterable
    {
        return $this->createQueryBuilder('cartSavings')
            ->addSelect('account')
            ->innerJoin('cartSavings.account', 'account')
            ->andWhere('cartSavings.orderId IS NOT NULL')
            ->andWhere('LENGTH(cartSavings.orderId) > :minOrderIdLength')
            ->andWhere('cartSavings.orderState IS NULL OR cartSavings.orderState NOT IN (:terminalStates)')
            ->setParameter('minOrderIdLength', self::DJUST_ORDER_ID_MIN_LENGTH)
            ->setParameter('terminalStates', self::TERMINAL_ORDER_STATES)
            ->orderBy('cartSavings.updatedAt', 'ASC')
            ->addOrderBy('cartSavings.createdAt', 'ASC')
            ->getQuery()
            ->toIterable();
    }
}
