<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserInfoUpdateRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserInfoUpdateRequest>
 *
 * @method null|UserInfoUpdateRequest find($id, $lockMode = null, $lockVersion = null)
 * @method null|UserInfoUpdateRequest findOneBy(array $criteria, array $orderBy = null)
 * @method UserInfoUpdateRequest[]    findAll()
 * @method UserInfoUpdateRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserInfoUpdateRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserInfoUpdateRequest::class);
    }

    public function save(UserInfoUpdateRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserInfoUpdateRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function get(): void
    {
    }
}
