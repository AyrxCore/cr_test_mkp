<?php

declare(strict_types=1);

namespace App\DataProvider;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

class UserMeItemDataProvider extends AbstractItemDataProvider
{
    public function __construct(
        protected ManagerRegistry $managerRegistry,
        protected iterable $itemExtensions,
        private Security $security,
    ) {
        parent::__construct($managerRegistry, $itemExtensions);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $operationName === 'get_me' && parent::supports($resourceClass, $operationName, $context);
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        return parent::getItem($resourceClass, $currentUser->getId(), $operationName, $context);
    }

    protected function getRessourceClass(): string
    {
        return User::class;
    }
}
