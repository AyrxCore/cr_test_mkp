<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\Banner;

class BannerFactory extends AbstractFactory
{
    public function create(array $data): Banner
    {
        $bannerCached = $this->cache->getItem(\sprintf('banner_%d', $data['id']));
        if (!$bannerCached->isHit()) {
            $banner = new Banner();
            $banner->setId($data['id']);
            $banner->setSlug($data['slug']);
            foreach ($data['dynamic_fields'] as $value) {
                $fieldName = $value['dynamic_field_configuration']['name']['default'];
                $fieldValue = $value['value'];
                switch ($fieldName) {
                    case 'bandeau_flash_text':
                        $banner->setText($fieldValue);
                        break;
                    case 'bandeau_flash_cta_text':
                        $banner->setCtaTxt($fieldValue);
                        break;
                    case 'bandeau_flash_cta_link':
                        $banner->setCtaLink($fieldValue);
                        break;
                }
            }

            $bannerCached->set($banner);
            $bannerCached->expiresAfter(new \DateInterval('PT1H'));
            $this->cache->save($bannerCached);
        }

        return $bannerCached->get();
    }
}
