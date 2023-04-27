<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Account;
use App\Entity\Favorite;
use App\Service\FavoriteService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class FavoritePersister implements ContextAwareDataPersisterInterface
{
    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public NormalizerInterface $normalizer;

    #[Required]
    public Security $security;

    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public FavoriteService $favoriteService;

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Favorite;
    }

    /**
     * @param Favorite $data
     * @throws Exception
     */
    public function persist($data, array $context = [])
    {
        try {

            $session = $this->requestStack->getSession();
            if (isset($context["collection_operation_name"]) && ('create' === $context["collection_operation_name"])) {
                $favorite = new Favorite();

                $session->start();
                $account = $this->em->getRepository(Account::class)->find($session->get('account')->getId());
                $favorite->setAccount($account);
            } else  {
                $favorite = $this->em->getRepository(Favorite::class)->find($data->getId());
                if (!$this->security->isGranted('edit', [$favorite, $session->get('account')])) {
                    throw new \RuntimeException('Vous n\'êtes pas autorisé à modifier ce favori.');
                }

            }

            $favorite->setName($data->getName());
            $favorite->setPublic($data->isPublic());
            $this->em->persist($favorite);
            $this->em->flush();

            return $favorite;
        } catch (Exception $exception) {
            throw  new Exception($exception->getMessage());
        }

    }

    /**
     * @throws Exception
     */
    public function remove($data, array $context = []): bool
    {
        return $this->favoriteService->removeFavorite($data->getId());
    }
}
