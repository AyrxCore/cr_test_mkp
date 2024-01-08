<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Account;
use App\Entity\Favorite;
use App\Service\FavoriteService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class FavoritePersister implements ContextAwareDataPersisterInterface
{
    public function __construct(public EntityManagerInterface $em, public NormalizerInterface $normalizer, public Security $security, public RequestStack $requestStack, public
    FavoriteService $favoriteService ) {}

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Favorite;
    }

    /**
     * @param Favorite $data
     * @throws Exception
     */
    public function persist($data, array $context = []): Favorite
    {
        try {
            if (($context['collection_operation_name'] ?? null) === 'create') {
                $session = $this->requestStack->getSession();
                $account = $this->em->getRepository(Account::class)->find($session->get('account')->getId());
                $data->setAccount($account);
            }

            $this->em->persist($data);
            $this->em->flush();

            return $data;
        } catch (UniqueConstraintViolationException $uniqueConstraintViolationException) {
            throw new ConflictHttpException($uniqueConstraintViolationException->getMessage());
        } catch (Exception $exception) {
            throw  new Exception($exception->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function remove($data, array $context = []): bool
    {
        $this->em->remove($data);
        $this->em->flush();

        return true;
    }
}
