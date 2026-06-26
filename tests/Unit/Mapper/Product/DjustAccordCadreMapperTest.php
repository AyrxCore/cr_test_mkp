<?php

declare(strict_types=1);

use App\Dto\Product;
use App\Entity\Account;
use App\Entity\Adherent;
use App\Enum\Djust\DjustProductType;
use App\Mapper\Product\DjustAccordCadreMapper;
use App\Repository\AccountRepository;
use App\Repository\AccordStatutRepository;
use Symfony\Component\Uid\Uuid;

\beforeEach(function () {
    $this->accountRepository = Mockery::mock(AccountRepository::class);
    $this->accordStatutRepository = Mockery::mock(AccordStatutRepository::class);

    $this->mapper = new DjustAccordCadreMapper(
        $this->accountRepository,
        $this->accordStatutRepository,
    );
    $this->product = new Product();
    $this->product->setId('123');
});

\afterEach(function () {
    Mockery::close();
});

\it('does nothing when product type is not ACCORD_CADRE', function () {
    $masterProduct = [];
    $productType = DjustProductType::SELLABLE;
    $account = Mockery::mock(Account::class);

    $this->mapper->mapAccordCadre($this->product, $masterProduct, $productType, $account);

    \expect($this->product->getAccountAccordCadre())->toBeNull();
})->group('djust-accord-cadre-mapper');


\it('does nothing when accord id is missing from product', function () {
    $masterProduct = [];
    $productType = DjustProductType::ACCORD_CADRE;
    
    // Product n'a pas d'accordId (devrait être mappé par DjustAccordMapper)
    $account = Mockery::mock(Account::class);
    $account->shouldReceive('getId')->andReturn(Uuid::v4());

    $this->mapper->mapAccordCadre($this->product, $masterProduct, $productType, $account);

    \expect($this->product->getAccountAccordCadre())->toBeNull();
})->group('djust-accord-cadre-mapper');

\it('does nothing when accord id is null on product', function () {
    $masterProduct = [];
    $productType = DjustProductType::ACCORD_CADRE;

    // Set accordId to null explicitement
    $this->product->setAccordId(null);
    
    $account = Mockery::mock(Account::class);
    $account->shouldReceive('getId')->andReturn(Uuid::v4());

    $this->mapper->mapAccordCadre($this->product, $masterProduct, $productType, $account);

    \expect($this->product->getAccountAccordCadre())->toBeNull();
})->group('djust-accord-cadre-mapper');

\it('throws exception when account not found in repository', function () {
    $accountId = Uuid::v4();
    $accordIdValue = 'ACCORD-123';
    
    // Simuler que l'accordId a été mappé par DjustAccordMapper
    $this->product->setAccordId($accordIdValue);
    
    $masterProduct = [];
    $productType = DjustProductType::ACCORD_CADRE;

    $account = Mockery::mock(Account::class);
    $account->shouldReceive('getId')->andReturn($accountId);

    $this->accountRepository->shouldReceive('find')
        ->once()
        ->with($accountId)
        ->andReturn(null);

    $this->mapper->mapAccordCadre($this->product, $masterProduct, $productType, $account);
})->throws(\RuntimeException::class, 'Compte introuvable pour l\'accord-cadre')
  ->group('djust-accord-cadre-mapper');

\it('maps accord cadre with valid data when no statut found', function () {
    $accountId = Uuid::v4();
    $adherentId = Uuid::v4();
    $accordIdValue = 'ACCORD-123';

    // Simuler que l'accordId a été mappé par DjustAccordMapper
    $this->product->setAccordId($accordIdValue);
    
    $masterProduct = [];
    $productType = DjustProductType::ACCORD_CADRE;

    $adherent = Mockery::mock(Adherent::class);
    $adherent->shouldReceive('getId')->andReturn($adherentId);

    $managedAccount = Mockery::mock(Account::class);
    $managedAccount->shouldReceive('getId')->andReturn($accountId);
    $managedAccount->shouldReceive('getAdherent')->andReturn($adherent);

    $account = Mockery::mock(Account::class);
    $account->shouldReceive('getId')->andReturn($accountId);

    $this->accountRepository->shouldReceive('find')
        ->once()
        ->with($accountId)
        ->andReturn($managedAccount);

    $this->accordStatutRepository->shouldReceive('findOneBy')
        ->once()
        ->with([
            'adherent' => $adherentId,
            'accordId' => $accordIdValue,
        ])
        ->andReturn(null);

    $this->mapper->mapAccordCadre($this->product, $masterProduct, $productType, $account);

    $accordCadre = $this->product->getAccountAccordCadre();
    \expect($accordCadre)->not()->toBeNull();
    \expect($accordCadre->getStatus())->toBe(\App\Dto\AccountAccordCadre::PROCESS_STATUS_NOT_ACTIVATED);
})->group('djust-accord-cadre-mapper');

\it('handles invalid UUID accord id gracefully', function () {
    $accountId = Uuid::v4();
    $adherentId = Uuid::v4();
    $accordIdValue = 'NOT-A-UUID';

    // Simuler que l'accordId a été mappé par DjustAccordMapper
    $this->product->setAccordId($accordIdValue);
    
    $masterProduct = [];
    $productType = DjustProductType::ACCORD_CADRE;

    $adherent = Mockery::mock(Adherent::class);
    $adherent->shouldReceive('getId')->andReturn($adherentId);

    $managedAccount = Mockery::mock(Account::class);
    $managedAccount->shouldReceive('getId')->andReturn($accountId);
    $managedAccount->shouldReceive('getAdherent')->andReturn($adherent);

    $account = Mockery::mock(Account::class);
    $account->shouldReceive('getId')->andReturn($accountId);

    $this->accountRepository->shouldReceive('find')
        ->once()
        ->with($accountId)
        ->andReturn($managedAccount);

    $this->accordStatutRepository->shouldReceive('findOneBy')
        ->once()
        ->andReturn(null);

    $this->mapper->mapAccordCadre($this->product, $masterProduct, $productType, $account);

    $accordCadre = $this->product->getAccountAccordCadre();
    \expect($accordCadre)->not()->toBeNull();
    // L'accord ID ne devrait pas être set car UUID invalide
    \expect($accordCadre->getAccordId())->toBeNull();
})->group('djust-accord-cadre-mapper');

\it('handles valid UUID accord id', function () {
    $accountId = Uuid::v4();
    $adherentId = Uuid::v4();
    $accordIdValue = Uuid::v4()->toRfc4122();

    // Simuler que l'accordId a été mappé par DjustAccordMapper
    $this->product->setAccordId($accordIdValue);
    
    $masterProduct = [];
    $productType = DjustProductType::ACCORD_CADRE;

    $adherent = Mockery::mock(Adherent::class);
    $adherent->shouldReceive('getId')->andReturn($adherentId);

    $managedAccount = Mockery::mock(Account::class);
    $managedAccount->shouldReceive('getId')->andReturn($accountId);
    $managedAccount->shouldReceive('getAdherent')->andReturn($adherent);

    $account = Mockery::mock(Account::class);
    $account->shouldReceive('getId')->andReturn($accountId);

    $this->accountRepository->shouldReceive('find')
        ->once()
        ->with($accountId)
        ->andReturn($managedAccount);

    $this->accordStatutRepository->shouldReceive('findOneBy')
        ->once()
        ->andReturn(null);

    $this->mapper->mapAccordCadre($this->product, $masterProduct, $productType, $account);

    $accordCadre = $this->product->getAccountAccordCadre();
    \expect($accordCadre)->not()->toBeNull();
    \expect($accordCadre->getAccordId())->toBeInstanceOf(Uuid::class);
})->group('djust-accord-cadre-mapper');

