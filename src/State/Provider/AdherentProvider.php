<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\AdherentRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class AdherentProvider implements ProviderInterface
{
    public function __construct(
        private AdherentRepository $adherentRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $adherent = $this->adherentRepository->find($uriVariables['id']);

        if (!$adherent) {
            throw new NotFoundHttpException('Adherent not found');
        }

        return $adherent;
    }
}
