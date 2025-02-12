<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\ExpertContent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExpertContentFactory extends AbstractFactory
{
    public function create(array $data): ExpertContent
    {
        $expertContentCached = $this->cache->getItem(\sprintf('expert_content_%d', $data['id']));
        if (!$expertContentCached->isHit()) {
            $id = $data['id'];
            $slug = $data['slug'];

            $expertContent = new ExpertContent();
            $expertContent->setId($id);
            $expertContent->setSlug($slug);

            foreach ($data['dynamic_fields'] as $dynamicField) {
                $fieldName = $dynamicField['dynamic_field_configuration']['name']['fr'];

                switch ($fieldName) {
                    case 'mise_en_avant_homepage_img_desktop':
                        if (\count($dynamicField['images']) !== 1) {
                            throw new NotFoundHttpException('mise_en_avant_homepage_img_desktop img is empty');
                        }

                        $expertContent->setMiseEnAvantHomepageImgDesktop($dynamicField['images'][0]['url']);
                        break;

                    case 'mise_en_avant_homepage_img_mobile':
                        if (\count($dynamicField['images']) !== 1) {
                            throw new NotFoundHttpException('mise_en_avant_homepage_img_mobile img is empty');
                        }

                        $expertContent->setMiseEnAvantHomepageImgMobile($dynamicField['images'][0]['url']);
                        break;

                    case 'page_actus_img_desktop':
                        if (\count($dynamicField['images']) !== 1) {
                            throw new NotFoundHttpException('page_actus_img_desktop img is empty');
                        }
                        $expertContent->setPageActusImgDesktop($dynamicField['images'][0]['url']);
                        break;

                    case 'page_actus_img_mobile':
                        if (\count($dynamicField['images']) !== 1) {
                            throw new NotFoundHttpException('page_actus_img_mobile img is empty');
                        }
                        $expertContent->setPageActusImgMobile($dynamicField['images'][0]['url']);

                        break;

                    case 'slider_img_desktop':
                        if (\count($dynamicField['images']) === 1) {
                            $expertContent->setSliderImgDesktop($dynamicField['images'][0]['url']);
                        }

                        break;

                    case 'slider_img_mobile':
                        if (\count($dynamicField['images']) === 1) {
                            $expertContent->setSliderImgMobile($dynamicField['images'][0]['url']);
                        }

                        break;

                    case 'article_img_desktop':
                        if (\count($dynamicField['images']) !== 1) {
                            throw new NotFoundHttpException('article_img_desktop img is empty');
                        }

                        $expertContent->setArticleImgDesktop($dynamicField['images'][0]['url']);
                        break;

                    case 'article_img_mobile':
                        if (\count($dynamicField['images']) !== 1) {
                            throw new NotFoundHttpException('article_img_mobile img is empty');
                        }

                        $expertContent->setArticleImgMobile($dynamicField['images'][0]['url']);
                        break;

                    case 'article_title':
                        if (empty($dynamicField['value'])) {
                            throw new NotFoundHttpException('article_title value is empty');
                        }

                        $expertContent->setArticleTitle($dynamicField['value']);
                        break;

                    case 'date':
                        if (empty($dynamicField['value'])) {
                            throw new NotFoundHttpException('date value is empty');
                        }

                        $date = new \DateTime($dynamicField['value']);
                        $expertContent->setDate($date);
                        break;

                    case 'article_teaser':
                        $expertContent->setArticleTeaser($dynamicField['value'] ?? '');
                        break;

                    case 'article_content':
                        $expertContent->setArticleContent($dynamicField['value'] ?? '');
                        break;

                    case 'cta_txt':
                        $expertContent->setCtaTxt($dynamicField['value'] ?? '');
                        break;

                    case 'cta_link':
                        $expertContent->setCtaLink($dynamicField['value'] ?? '');
                        break;

                    case 'category_name':
                        if (empty($dynamicField['choices'][0]['value'])) {
                            throw new NotFoundHttpException('category_name value is empty');
                        }

                        $expertContent->setCategoryName($dynamicField['choices'][0]['value']);
                        break;
                }
            }

            $expertContent->setCategoryColor('');

            $expertContentCached->set($expertContent);
            $expertContentCached->expiresAfter(new \DateInterval('PT1H')); // the item will be cached for 1 HOUR
            $this->cache->save($expertContentCached);
        }

        return $expertContentCached->get();
    }
}
