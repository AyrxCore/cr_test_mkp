<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\AccordStoreView;
use App\Dto\AccordView;
use App\Entity\Accord;
use App\Helper\Formatter\PhoneFormatter;
use App\Repository\AccordRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class AccordProvider implements ProviderInterface
{
    public function __construct(
        private AccordRepository $accordRepository,
        private PhoneFormatter $phoneFormatter
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AccordView
    {
        $accord = $this->accordRepository->find($uriVariables['id']);

        if (!$accord) {
            throw new NotFoundHttpException('Accord not found');
        }

        $stores = [];
        if ($accord->hasStore()) {
            $directStores = $this->getAccordStores($accord);
            if (!empty($directStores)) {
                $stores = $directStores;
            } else {
                $stores = $this->getPartnerStores($accord);
            }
        }

        return new AccordView(
            (string) $accord->getId(),
            $accord->getName(),
            $accord->getLogo(),
            $stores,
        );
    }

    private function getAccordStores(Accord $accord): array
    {
        return $this->mapStoresToViews($accord->getStores()->toArray());
    }

    private function getPartnerStores(Accord $accord): array
    {
        return $this->mapStoresToViews($accord->getPartner()->getPartnerStores()->toArray());
    }

    private function mapStoresToViews(array $stores): array
    {
        return \array_map(
            fn ($store) => new AccordStoreView(
                (string) $store->getId(),
                $store->getName(),
                $store->getAddress(),
                $store->getLatitude(),
                $store->getLongitude(),
                $this->phoneFormatter->format($store->getPhone()),
            ),
            $stores
        );
    }
}
