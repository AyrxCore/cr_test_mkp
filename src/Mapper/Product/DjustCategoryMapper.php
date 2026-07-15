<?php

declare(strict_types=1);

namespace App\Mapper\Product;

use App\Dto\Product;
use App\Factory\CategoryFactory;

class DjustCategoryMapper
{
    public function __construct(
        private readonly CategoryFactory $categoryFactory,
    ) {
    }

    public function mapCategories(Product $product, array $masterProduct): void
    {
        $navigationCategories = $masterProduct['navigationCategories'] ?? [];

        $categories = $this->categoryFactory->createAndAddToCollection($navigationCategories) ?? [];

        $product->setCategories($categories);
    }
}
