<?php

declare(strict_types=1);

use App\Dto\Cart;
use App\Factory\DjustCartFactory;
use App\Mapper\Cart\DjustCartMapper;
use App\Service\Djust\DjustProductService;

\uses()->group('UnitDjustCartFactory');

\beforeEach(function () {
    $this->cartMapper = Mockery::mock(DjustCartMapper::class);
    $this->djustProductService = Mockery::mock(DjustProductService::class);
    $this->factory = new DjustCartFactory($this->cartMapper, $this->djustProductService);
});

\afterEach(function () {
    Mockery::close();
});

it('delegates to cart mapper', function () {
    $data = [
        'reference' => 'ORDER-123',
        'orderLogistics' => [
            [
                'lines' => [
                    [
                        'offerInventorySnapshotDto' => [
                            'offerInventoryExternalId' => 'INV-1'
                        ],
                    ],
                ],
            ],
        ],
    ];

    $offerPrices = ['content' => [/* ... */]];
    $expectedCart = new Cart();

    $this->djustProductService
        ->shouldReceive('getOffersByOfferInventory')
        ->once()
        ->with('INV-1')
        ->andReturn($offerPrices);

    $expectedEnrichedData = [
        'reference' => 'ORDER-123',
        'orderLogistics' => [
            [
                'lines' => [
                    [
                        'offerInventorySnapshotDto' => [
                            'offerInventoryExternalId' => 'INV-1'
                        ],
                        'offerPrices' => $offerPrices,
                    ],
                ],
            ],
        ],
    ];

    $this->cartMapper
        ->shouldReceive('mapCommercialOrderToCart')
        ->once()
        ->with($expectedEnrichedData)
        ->andReturn($expectedCart);

    $result = $this->factory->createFromCommercialOrder($data);

    expect($result)->toBe($expectedCart);
});
