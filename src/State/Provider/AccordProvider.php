<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\AccordStoreView;
use App\Dto\AccordView;
use App\Entity\Accord;
use App\Repository\AccordRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class AccordProvider implements ProviderInterface
{
    public function __construct(
        private AccordRepository $accordRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AccordView
    {
        $accord = $this->accordRepository->find($uriVariables['id']);

        if (!$accord) {
            throw new NotFoundHttpException('Accord not found');
        }

        $stores = $accord->hasStore() ? $this->getStores($accord) : [];

        return new AccordView(
            (string) $accord->getId(),
            $accord->getName(),
            $accord->getLogo(),
            $stores,
        );
    }

    private function getStores(Accord $accord): array
    {
        $partnerStores = $accord->getStores()->count() === 0
            ? $accord->getPartner()->getPartnerStores()->toArray()
            : $accord->getStores()->toArray();

        return \array_map(
            fn ($store) => new AccordStoreView(
                $store->getName(),
                $store->getAddress(),
                $store->getLatitude(),
                $store->getLongitude()
            ),
            $partnerStores
        );
    }
}
