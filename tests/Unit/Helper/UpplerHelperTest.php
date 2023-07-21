<?php

declare(strict_types=1);

use App\Helper\UpplerHelper;

\it('formats prices', function (int $price, float $expectedFormattedPrice) {
    $formattedPrice = UpplerHelper::formatPrice($price);

    \expect($formattedPrice)
        ->toBe($expectedFormattedPrice);
})
    ->with([
        ['price' => 1999, 'expectedFormattedPrice' => 19.99],
        ['price' => 23400000, 'expectedFormattedPrice' => 234000.0],
    ])
    ->group('helper');
