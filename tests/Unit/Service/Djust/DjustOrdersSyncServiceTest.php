<?php

declare(strict_types=1);

use App\Entity\CartSavings;
use App\Repository\CartSavingsRepository;
use App\Service\Djust\DjustDataExtractor;
use App\Service\Djust\DjustOrderService;
use App\Service\Djust\DjustOrdersSyncService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\ResponseInterface;

\uses()->group('UnitDjustOrdersSyncService', 'Djust');

\beforeEach(function () {
    $this->cartSavingsRepository = Mockery::mock(CartSavingsRepository::class);
    $this->djustOrderService = Mockery::mock(DjustOrderService::class);
    $this->entityManager = Mockery::mock(EntityManagerInterface::class);
    $this->logger = Mockery::mock(LoggerInterface::class);

    $this->service = new DjustOrdersSyncService(
        $this->cartSavingsRepository,
        $this->djustOrderService,
        new DjustDataExtractor(),
        $this->entityManager,
        $this->logger,
    );
});

\afterEach(function () {
    Mockery::close();
});

\it('updates order state when status changed', function () {
    $saving = (new CartSavings())
        ->setOrderId('0000000123')
        ->setOrderState('pending');

    $this->cartSavingsRepository->shouldReceive('iterateWithOrderId')->once()->andReturn([$saving]);
    $this->djustOrderService
        ->shouldReceive('getOrderByIdForAccount')
        ->once()
        ->with('0000000123', null)
        ->andReturn(['status' => 'shipped']);
    $this->cartSavingsRepository->shouldReceive('save')->once();
    $this->entityManager->shouldReceive('flush')->once();
    $this->logger->shouldNotReceive('error');

    $result = $this->service->sync();

    \expect($result)->toBe(['processed' => 1, 'updated' => 1, 'skipped' => 0, 'failed' => 0]);
    \expect($saving->getOrderState())->toBe('shipped');
});

\it('skips order when status unchanged', function () {
    $saving = (new CartSavings())
        ->setOrderId('0000000456')
        ->setOrderState('confirmed');

    $this->cartSavingsRepository->shouldReceive('iterateWithOrderId')->once()->andReturn([$saving]);
    $this->djustOrderService
        ->shouldReceive('getOrderByIdForAccount')
        ->once()
        ->andReturn(['status' => 'confirmed']);
    $this->cartSavingsRepository->shouldNotReceive('save');
    $this->entityManager->shouldReceive('flush')->once();

    $result = $this->service->sync();

    \expect($result)->toBe(['processed' => 1, 'updated' => 0, 'skipped' => 1, 'failed' => 0]);
});

\it('skips order when no orderId', function () {
    $saving = new CartSavings();

    $this->cartSavingsRepository->shouldReceive('iterateWithOrderId')->once()->andReturn([$saving]);
    $this->djustOrderService->shouldNotReceive('getOrderByIdForAccount');
    $this->entityManager->shouldReceive('flush')->once();

    $result = $this->service->sync();

    \expect($result)->toBe(['processed' => 1, 'updated' => 0, 'skipped' => 1, 'failed' => 0]);
});

\it('skips with warning when Djust order not found (null)', function () {
    $saving = (new CartSavings())->setOrderId('0000000789');

    $this->cartSavingsRepository->shouldReceive('iterateWithOrderId')->once()->andReturn([$saving]);
    $this->djustOrderService
        ->shouldReceive('getOrderByIdForAccount')
        ->once()
        ->andReturn(null);
    $this->entityManager->shouldReceive('flush')->once();
    $this->logger->shouldReceive('warning')->once();

    $result = $this->service->sync();

    \expect($result)->toBe(['processed' => 1, 'updated' => 0, 'skipped' => 1, 'failed' => 0]);
});

\it('skips with warning on 404 ClientException', function () {
    $saving = (new CartSavings())->setOrderId('0000000999');

    $response = Mockery::mock(ResponseInterface::class);
    $response->shouldReceive('getStatusCode')->andReturn(404);
    $response->shouldReceive('getInfo')->with('http_code')->andReturn(404);
    $response->shouldReceive('getInfo')->with('url')->andReturn('https://djust.test/orders/0000000999');
    $response->shouldReceive('getInfo')->with('response_headers')->andReturn([]);
    $response->shouldReceive('getInfo')->andReturn(null);
    $response->shouldReceive('getContent')->andReturn('');
    $exception = new ClientException($response);

    $this->cartSavingsRepository->shouldReceive('iterateWithOrderId')->once()->andReturn([$saving]);
    $this->djustOrderService->shouldReceive('getOrderByIdForAccount')->once()->andThrow($exception);
    $this->entityManager->shouldReceive('flush')->once();
    $this->logger->shouldReceive('warning')->once();

    $result = $this->service->sync();

    \expect($result)->toBe(['processed' => 1, 'updated' => 0, 'skipped' => 1, 'failed' => 0]);
});

\it('counts as failed on non-404 exception', function () {
    $saving = (new CartSavings())->setOrderId('0000001000');

    $this->cartSavingsRepository->shouldReceive('iterateWithOrderId')->once()->andReturn([$saving]);
    $this->djustOrderService
        ->shouldReceive('getOrderByIdForAccount')
        ->once()
        ->andThrow(new RuntimeException('Djust unavailable'));
    $this->entityManager->shouldReceive('flush')->once();
    $this->logger->shouldReceive('error')->once();

    $result = $this->service->sync();

    \expect($result)->toBe(['processed' => 1, 'updated' => 0, 'skipped' => 0, 'failed' => 1]);
});

\it('matches the orderLogistic by sellerId instead of always using the first one (MKP-1557)', function () {
    // Une commande a un orderLogistic par vendeur : il ne faut pas se fier au premier de la liste
    // pour une ligne CartSavings qui appartient à un autre vendeur.
    $saving = (new CartSavings())
        ->setOrderId('0000625780')
        ->setSellerId(69792)
        ->setOrderState('WAITING_SHIPMENT');

    $order = [
        'status' => 'WAITING_SHIPMENT',
        'orderLogistics' => [
            ['id' => '0004078562', 'status' => 'WAITING_SHIPMENT', 'supplierSnapshot' => ['id' => 69770]],
            ['id' => '0004078566', 'status' => 'WAITING_SUPPLIER_APPROVAL', 'supplierSnapshot' => ['id' => 69792]],
        ],
    ];

    $this->cartSavingsRepository->shouldReceive('iterateWithOrderId')->once()->andReturn([$saving]);
    $this->djustOrderService->shouldReceive('getOrderByIdForAccount')->once()->andReturn($order);
    $this->cartSavingsRepository->shouldReceive('save')->once();
    $this->entityManager->shouldReceive('flush')->once();

    $result = $this->service->sync();

    \expect($result)->toBe(['processed' => 1, 'updated' => 1, 'skipped' => 0, 'failed' => 0]);
    // Doit prendre le statut du orderLogistic du vendeur 69792, pas celui du premier (69770)
    \expect($saving->getOrderState())->toBe('WAITING_SUPPLIER_APPROVAL');
    \expect($saving->getSellerOrderId())->toBe('0004078566');
});

\it('returns empty stats when no CartSavings with orderId', function () {
    $this->cartSavingsRepository->shouldReceive('iterateWithOrderId')->once()->andReturn([]);
    $this->entityManager->shouldReceive('flush')->once();

    $result = $this->service->sync();

    \expect($result)->toBe(['processed' => 0, 'updated' => 0, 'skipped' => 0, 'failed' => 0]);
});





