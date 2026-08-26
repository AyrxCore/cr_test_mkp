<?php

declare(strict_types=1);

namespace App\Mapper\Product;

use App\Dto\AccountAccordCadre;
use App\Dto\Product;
use App\Entity\Account;
use App\Enum\Djust\DjustProductType;
use App\Repository\AccordStatutRepository;
use App\Repository\AccountRepository;
use Symfony\Component\Uid\Uuid;

class DjustAccordCadreMapper
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly AccordStatutRepository $accordStatutRepository,
    ) {
    }

    public function mapAccordCadre(
        Product $product,
        array $masterProduct,
        DjustProductType $productType,
        Account $account,
    ): void {
        if ($productType !== DjustProductType::ACCORD_CADRE) {
            return;
        }

        $accordIdValue = $product->getAccordId();
        if ($accordIdValue === null) {
            return;
        }

        $managedAccount = $this->accountRepository->find($account->getId());
        if ($managedAccount === null) {
            throw new \RuntimeException('Compte introuvable pour l\'accord-cadre');
        }

        $accordStatut = $this->accordStatutRepository->findOneBy([
            'adherent' => $managedAccount->getAdherent()->getId(),
            'accordId' => $accordIdValue,
        ]);

        $status = $accordStatut ? $accordStatut->getStatus() : AccountAccordCadre::PROCESS_STATUS_NOT_ACTIVATED;

        $accountAccordCadre = new AccountAccordCadre();
        $accountAccordCadre->setStatus($status);

        if (Uuid::isValid($accordIdValue)) {
            $accountAccordCadre->setAccordId(Uuid::fromString($accordIdValue));
        }

        $product->setAccountAccordCadre($accountAccordCadre);
    }
}
