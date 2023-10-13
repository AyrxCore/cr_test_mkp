<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\ExpertContentCategory;

class ExpertContentCategoryFactory extends AbstractFactory
{
    public function create(array $data): ExpertContentCategory
    {
        $expertContentCategoryCached = $this->cache->getItem(\sprintf('expert_content_category_%d', $data['id'][0]));
        if (!$expertContentCategoryCached->isHit()) {
            $expertContentCategory = new ExpertContentCategory();
            $expertContentCategory->setId($data['id'][0]);
            $expertContentCategory->setName($data['name'][0]);
            $expertContentCategory->setColor($data['color'][0]);

            $expertContentCategoryCached->set($expertContentCategory);
            $expertContentCategoryCached->expiresAfter(new \DateInterval('PT1H')); // the item will be cached for 1 HOUR
            $this->cache->save($expertContentCategoryCached);
        }

        return $expertContentCategoryCached->get();
    }
}
