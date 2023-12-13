<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Context\ChannelContext;
use App\Entity\Adherent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdherentDataProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{
    public function __construct(private EntityManagerInterface $entityManager, private ChannelContext $channelContext)
    {
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $channel = $this->channelContext->getChannel();

        $adherent = $this->entityManager->getRepository(Adherent::class)->find($id);

        if (!$adherent) {
            throw new NotFoundHttpException('Adherent not found');
        }
        
        if ($adherent->getChannel() !== $channel) {
            throw new AccessDeniedException('Access to channel is forbidden');
        }

        return $adherent;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Adherent::class;
    }
}
