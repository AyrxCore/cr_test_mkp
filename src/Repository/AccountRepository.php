<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Account>
 *
 * @method null|Account find($id, $lockMode = null, $lockVersion = null)
 * @method null|Account findOneBy(array $criteria, array $orderBy = null)
 * @method Account[]    findAll()
 * @method Account[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    public function save(Account $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Account $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAccountsByUserEmailAndChannelCode(string $email, string $channelCode): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.adherent', 'adherent')
            ->join('adherent.channel', 'channel')
            ->join('a.user', 'user')
            ->where('user.email = :email')
            ->andWhere('channel.code = :channelCode')
            ->setParameter('email', $email)
            ->setParameter('channelCode', $channelCode)
            ->getQuery()
            ->getResult();
    }
}
