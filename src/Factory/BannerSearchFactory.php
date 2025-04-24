<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\BannerSearch;

class BannerSearchFactory extends AbstractFactory
{
    public function create(array $data): BannerSearch
    {
        $bannerSearchCached = $this->cache->getItem(\sprintf('banner_search_%d', $data['id']));
        if (!$bannerSearchCached->isHit()) {
            $bannerSearch = new BannerSearch();
            $bannerSearch->setId($data['id']);
            foreach ($data['dynamic_fields'] as $value) {
                $fieldName = $value['dynamic_field_configuration']['name']['default'];
                $fieldValue = $value;
                switch ($fieldName) {
                    case 'banner_search_category':
                        $bannerSearch->setCategory($fieldValue['value']);
                        break;
                    case 'banner_search_desktop_img':
                        $bannerSearch->setDesktopImg($fieldValue['images'][0]['url']);
                        break;
                    case 'banner_search_mobile_img':
                        $bannerSearch->setMobileImg($fieldValue['images'][0]['url']);
                        break;
                    case 'banner_search_link':
                        $bannerSearch->setLink($fieldValue['value']);
                        break;
                }
            }

            $bannerSearchCached->set($bannerSearch);
            $bannerSearchCached->expiresAfter(new \DateInterval('PT1H'));
            $this->cache->save($bannerSearchCached);
        }

        return $bannerSearchCached->get();
    }
}
