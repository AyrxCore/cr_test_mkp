<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Doctrine\Extension\CurrentUserAccountExtension;
use App\Repository\AccountRepository;

readonly class AccountProvider implements ProviderInterface
{
    public function __construct(protected CurrentUserAccountExtension $currentUserAccountExtension, private AccountRepository $accountRepository)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable
    {
        $queryBuilder = $this->accountRepository->createQueryBuilder('o');

        $this->currentUserAccountExtension->applyToCollection($queryBuilder, new QueryNameGenerator(), 'App\Entity\Account');

        foreach ($queryBuilder->getQuery()->execute() as $object) {
            if (!$object->isEnabled() || !$object->getAdherent()?->isActive()) {
                continue;
            }
            yield $object;
        }
    }
}
