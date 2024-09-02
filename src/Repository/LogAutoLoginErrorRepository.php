<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\LogAutoLoginError;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogAutoLoginError>
 *
 * @method null|LogAutoLoginError find($id, $lockMode = null, $lockVersion = null)
 * @method null|LogAutoLoginError findOneBy(array $criteria, array $orderBy = null)
 * @method LogAutoLoginError[]    findAll()
 * @method LogAutoLoginError[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogAutoLoginErrorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogAutoLoginError::class);
    }

    public function save(LogAutoLoginError $logAutoLoginException, bool $flush = true): void
    {
        $this->_em->persist($logAutoLoginException);

        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getPaginatedLogs(int $page = 1, int $perPage = 30): Paginator
    {
        $qb = $this->createQueryBuilder('l')
            ->orderBy('l.createdAt', 'DESC')
        ->getQuery();

        $paginator = new Paginator($qb);
        $paginator
            ->getQuery()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        return $paginator;
    }
}
