<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Channel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ChannelDataProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $channel = $this->entityManager->getRepository(Channel::class)->findOneBy(['hostname' => $id]);

        if (!$channel) {
            throw new NotFoundHttpException('Channel not found');
        }

        return $channel;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Channel::class === $resourceClass && $operationName === 'get_by_host';
    }
}
