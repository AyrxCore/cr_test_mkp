<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\LogAutoLoginError;
use App\Repository\LogAutoLoginErrorRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

readonly class LogAutoLoginErrorProvider implements ProviderInterface
{
    public function __construct(private readonly LogAutoLoginErrorRepository $logAutoLoginExceptionRepository)
    {
    }

    /**
     * @throws \Exception
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Paginator
    {
        if ($operation instanceof CollectionOperationInterface) {
            $page = $context['filters']['page'] ?? 1;
            $perPage = $context['filters']['itemsPerPage'] ?? LogAutoLoginError::DEFAULT_ITEMS_PER_PAGE;

            return $this->logAutoLoginExceptionRepository->getPaginatedLogs((int) $page, (int) $perPage);
        }

        throw new \RuntimeException('Unsupported operation');
    }
}
