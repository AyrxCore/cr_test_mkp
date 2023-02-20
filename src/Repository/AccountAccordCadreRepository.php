<?php

namespace App\Repository;

use App\Entity\AccountAccordCadre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<AccountAccordCadre>
 *
 * @method AccountAccordCadre|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccountAccordCadre|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccountAccordCadre[]    findAll()
 * @method AccountAccordCadre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountAccordCadreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccountAccordCadre::class);
    }

    public function save(AccountAccordCadre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AccountAccordCadre $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findByAccordAndAccountId(int $accordCadreId, string $accountId): array
    {
        $uuid = Uuid::fromString($accountId);
        return $this->createQueryBuilder('a')
            ->andWhere('a.accordCadreId = :accordCadreId')
            ->andWhere('a.accountId = :accountId')
            ->setParameter('accordCadreId', $accordCadreId)
            ->setParameter('accountId', $uuid)
            ->getQuery()
            ->getSingleResult()
       ;
    }
}
