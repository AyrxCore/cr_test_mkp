<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Context\ChannelContext;
use App\Repository\AdherentRepository;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class AdherentProvider implements ProviderInterface
{
    public function __construct(
        private AdherentRepository $adherentRepository,
        private ChannelContext $channelContext,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $channel = $this->channelContext->getChannel();

        $adherent = $this->adherentRepository->find($uriVariables['id']);

        if (!$adherent) {
            throw new NotFoundHttpException('Adherent not found');
        }

        if ($adherent->getChannel() !== $channel) {
            throw new AccessDeniedException('Access to channel is forbidden');
        }

        return $adherent;
    }
}
