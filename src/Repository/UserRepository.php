<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Channel;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method null|User find($id, $lockMode = null, $lockVersion = null)
 * @method null|User findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * The `email` column has no unique constraint in database, so several users can share the
     * same email address (only `username` is guaranteed unique). We therefore always limit the
     * query to a single row to avoid NonUniqueResultException.
     *
     * When a Channel is provided, we first try to return a user having an enabled account on
     * this channel, matching the business rule already applied by the callers. If none is
     * found (or no channel was given), we fall back to a single, deterministic match.
     */
    public function findUserByUsernameOrEmail(?string $value, ?Channel $channel = null): ?User
    {
        if ($value === null) {
            return null;
        }

        $value = \trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (!\mb_check_encoding($value, 'UTF-8')) {
            return null;
        }

        if ($channel !== null) {
            $user = $this->createQueryBuilder('u')
                ->join('u.accounts', 'a')
                ->join('a.adherent', 'ad')
                ->where('u.email = :value OR u.username = :value')
                ->andWhere('a.enabled = true')
                ->andWhere('ad.channel = :channel')
                ->setParameter('value', $value)
                ->setParameter('channel', $channel)
                ->orderBy('u.id', 'ASC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if ($user !== null) {
                return $user;
            }
        }

        return $this->createQueryBuilder('u')
            ->where('u.email = :value')
            ->orWhere('u.username = :value')
            ->setParameter('value', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(\sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }
}
