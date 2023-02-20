<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Api\Buyer\ProductApiController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[ApiResource(
    collectionOperations: [
        'get' => [
            "openapi_context" => [
                'summary'     => 'Liste des contenus experts',
                'description' => 'Permet de récupérer les derniers contenus experts',
            ],
            'path'            => '/experts_contents',
            'method'          => 'GET',
            'normalization'   => false,
        ],
    ],
    itemOperations: [
        'get' => [
            'path'         => '/expert_content/{id}',
            'requirements' => ['id' => '\d+'],
        ],
    ],
    formats: ['json'],
)]
class ExpertContent implements \JsonSerializable
{

    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    private string $slug = '';

    private string $mise_en_avant_homepage_img_desktop = '';

    private string $mise_en_avant_homepage_img_mobile = '';

    private string $page_actus_img_desktop = '';

    private string $page_actus_img_mobile = '';

    private string $slider_img_desktop = '';

    private string $slider_img_mobile = '';

    private string $article_img_desktop = '';

    private string $article_img_mobile = '';


    private string $articleTitle = '';

    private string $articleTeaser = '';

    private string $articleContent = '';

    private string $ctaTxt = '';

    private string $ctaLink = '';

    private string $categoryName = '';

    private ?string $categoryColor = '';

    private ?\DateTime $date = null;

    /**
     * @param  int|null  $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param  string  $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @param  string  $mise_en_avant_homepage_img_desktop
     */
    public function setMiseEnAvantHomepageImgDesktop(string $mise_en_avant_homepage_img_desktop): void
    {
        $this->mise_en_avant_homepage_img_desktop = $mise_en_avant_homepage_img_desktop;
    }

    /**
     * @param  string  $mise_en_avant_homepage_img_mobile
     */
    public function setMiseEnAvantHomepageImgMobile(string $mise_en_avant_homepage_img_mobile): void
    {
        $this->mise_en_avant_homepage_img_mobile = $mise_en_avant_homepage_img_mobile;
    }

    /**
     * @param  string  $page_actus_img_desktop
     */
    public function setPageActusImgDesktop(string $page_actus_img_desktop): void
    {
        $this->page_actus_img_desktop = $page_actus_img_desktop;
    }

    /**
     * @param  string  $page_actus_img_mobile
     */
    public function setPageActusImgMobile(string $page_actus_img_mobile): void
    {
        $this->page_actus_img_mobile = $page_actus_img_mobile;
    }

    /**
     * @param  string  $slider_img_desktop
     */
    public function setSliderImgDesktop(string $slider_img_desktop): void
    {
        $this->slider_img_desktop = $slider_img_desktop;
    }

    /**
     * @param  string  $slider_img_mobile
     */
    public function setSliderImgMobile(string $slider_img_mobile): void
    {
        $this->slider_img_mobile = $slider_img_mobile;
    }

    /**
     * @param  string  $article_img_desktop
     */
    public function setArticleImgDesktop(string $article_img_desktop): void
    {
        $this->article_img_desktop = $article_img_desktop;
    }

    /**
     * @param  string  $article_img_mobile
     */
    public function setArticleImgMobile(string $article_img_mobile): void
    {
        $this->article_img_mobile = $article_img_mobile;
    }


    /**
     * @param  string  $articleTitle
     */
    public function setArticleTitle(string $articleTitle): void
    {
        $this->articleTitle = $articleTitle;
    }

    /**
     * @param  string  $articleTeaser
     */
    public function setArticleTeaser(string $articleTeaser): void
    {
        $this->articleTeaser = $articleTeaser;
    }

    /**
     * @param  string  $articleContent
     */
    public function setArticleContent(string $articleContent): void
    {
        $this->articleContent = $articleContent;
    }

    /**
     * @param  string  $ctaTxt
     */
    public function setCtaTxt(string $ctaTxt): void
    {
        $this->ctaTxt = $ctaTxt;
    }

    /**
     * @param  string  $ctaLink
     */
    public function setCtaLink(string $ctaLink): void
    {
        $this->ctaLink = $ctaLink;
    }

    /**
     * @param  string  $categoryName
     */
    public function setCategoryName(string $categoryName): void
    {
        $this->categoryName = $categoryName;
    }

    /**
     * @param  string|null  $categoryColor
     */
    public function setCategoryColor(?string $categoryColor): void
    {
        $this->categoryColor = $categoryColor;
    }

    /**
     * @param  \DateTime|null  $date
     */
    public function setDate(?\DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getMiseEnAvantHomepageImgDesktop(): string
    {
        return $this->mise_en_avant_homepage_img_desktop;
    }

    /**
     * @return string
     */
    public function getMiseEnAvantHomepageImgMobile(): string
    {
        return $this->mise_en_avant_homepage_img_mobile;
    }

    /**
     * @return string
     */
    public function getPageActusImgDesktop(): string
    {
        return $this->page_actus_img_desktop;
    }

    /**
     * @return string
     */
    public function getPageActusImgMobile(): string
    {
        return $this->page_actus_img_mobile;
    }

    /**
     * @return string
     */
    public function getSliderImgDesktop(): string
    {
        return $this->slider_img_desktop;
    }

    /**
     * @return string
     */
    public function getSliderImgMobile(): string
    {
        return $this->slider_img_mobile;
    }

    /**
     * @return string
     */
    public function getArticleImgDesktop(): string
    {
        return $this->article_img_desktop;
    }

    /**
     * @return string
     */
    public function getArticleImgMobile(): string
    {
        return $this->article_img_mobile;
    }

    /**
     * @return string
     */
    public function getArticleTitle(): string
    {
        return $this->articleTitle;
    }

    /**
     * @return string
     */
    public function getArticleTeaser(): string
    {
        return $this->articleTeaser;
    }

    /**
     * @return string
     */
    public function getArticleContent(): string
    {
        return $this->articleContent;
    }

    /**
     * @return string
     */
    public function getCtaTxt(): string
    {
        return $this->ctaTxt;
    }

    /**
     * @return string
     */
    public function getCtaLink(): string
    {
        return $this->ctaLink;
    }

    /**
     * @return string
     */
    public function getCategoryName(): string
    {
        return $this->categoryName;
    }


    /**
     * @return string
     */
    public function getCategoryColor(): string
    {
        return $this->categoryColor;
    }

    /**
     * @return \DateTime|null
     */
    public function getDate(): ?\DateTime
    {
        return $this->date;
    }


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }


    public function hydrate($dynamicEntity)
    {
        $id = $dynamicEntity['id'];
        $slug = $dynamicEntity['slug'];

        $urlImg = 'https://uppler-platform-quantis.s3.eu-west-3.amazonaws.com/image/';

        foreach ($dynamicEntity['dynamic_fields'] as $dynamicField) {
            //                $fieldName = $dynamicField['dynamic_field_configuration']['name']['default'];
            $fieldName = $dynamicField['dynamic_field_configuration']['name']['fr'];

            switch ($fieldName) {
                case 'mise_en_avant_homepage_img_desktop':
                    if (count($dynamicField['images']) === 1) {
                        $mise_en_avant_homepage_img_desktop  = $urlImg . $dynamicField['images'][0]['path'];
                    } else {
                        throw new NotFoundHttpException('mise_en_avant_homepage_img_desktop img is empty');
                    }
                    break;

                case 'mise_en_avant_homepage_img_mobile':
                    if (count($dynamicField['images']) === 1) {
                        $mise_en_avant_homepage_img_mobile = $urlImg . $dynamicField['images'][0]['path'];
                    } else {
                        throw new NotFoundHttpException('mise_en_avant_homepage_img_mobile img is empty');
                    }
                    break;

                case 'page_actus_img_desktop':
                    if (count($dynamicField['images']) === 1) {
                        $page_actus_img_desktop = $urlImg . $dynamicField['images'][0]['path'];
                    } else {
                        throw new NotFoundHttpException('page_actus_img_desktop img is empty');
                    }
                    break;

                case 'page_actus_img_mobile':
                    if (count($dynamicField['images']) === 1) {
                        $page_actus_img_mobile = $urlImg . $dynamicField['images'][0]['path'];
                    } else {
                        throw new NotFoundHttpException('page_actus_img_mobile img is empty');
                    }
                    break;

                case 'slider_img_desktop':
                    if (count($dynamicField['images']) === 1) {
                        $slider_img_desktop = $urlImg . $dynamicField['images'][0]['path'];
                    } else {
                        throw new NotFoundHttpException('slider_img_desktop img is empty');
                    }
                    break;

                case 'slider_img_mobile':
                    if (count($dynamicField['images']) === 1) {
                        $slider_img_mobile = $urlImg . $dynamicField['images'][0]['path'];
                    } else {
                        throw new NotFoundHttpException('slider_img_mobile img is empty');
                    }
                    break;

                case 'article_img_desktop':
                    if (count($dynamicField['images']) === 1) {
                        $article_img_desktop = $urlImg . $dynamicField['images'][0]['path'];
                    } else {
                        throw new NotFoundHttpException('article_img_desktop img is empty');
                    }
                    break;

                case 'article_img_mobile':
                    if (count($dynamicField['images']) === 1) {
                        $article_img_mobile = $urlImg . $dynamicField['images'][0]['path'];
                    } else {
                        throw new NotFoundHttpException('article_img_mobile img is empty');
                    }
                    break;

                case 'article_title':
                    if (empty($dynamicField['value'])) {
                        throw new NotFoundHttpException('article_title value is empty');
                    } else {
                        $article_title = $dynamicField['value'];
                    }
                    break;

                case 'date':
                    if (empty($dynamicField['value'])) {
                        throw new NotFoundHttpException('date value is empty');
                    } else {
                        $date = new \DateTime($dynamicField['value']);
                    }
                    break;

                case 'article_teaser':
                    if (empty($dynamicField['value'])) {
                        throw new NotFoundHttpException('article_teaser value is empty');
                    } else {
                        $article_teaser = $dynamicField['value'];
                    }
                    break;

                case 'article_content':
                    if (empty($dynamicField['value'])) {
                        throw new NotFoundHttpException('article_content value is empty');
                    } else {
                        $article_content = $dynamicField['value'];
                    }
                    break;

                case 'cta_txt':
                    if (empty($dynamicField['value'])) {
                        $cta_txt = '';
                    } else {
                        $cta_txt = $dynamicField['value'];
                    }
                    break;

                case 'cta_link':
                    if (empty($dynamicField['value'])) {
                        $cta_link = '';
                    } else {
                        $cta_link = $dynamicField['value'];
                    }
                    break;

                case 'category_name':
                    if (empty($dynamicField['choices'][0]['value'])) {
                        throw new NotFoundHttpException('category_name value is empty');
                    } else {
                        $category_name = $dynamicField['choices'][0]['value'];
                    }
                    break;

                //                case 'category_color':
                //                    if (empty($dynamicField['choices'][0]['value'])) {
                //                        throw new NotFoundHttpException('category_color value is empty');
                //                    } else {
                //                        $category_color = $dynamicField['choices'][0]['value'];
                //                    }
                //                    break;
            }
        }

        $this->setId($id);
        $this->setSlug($slug);

        $this->setArticleImgMobile($article_img_mobile);
        $this->setArticleImgDesktop($article_img_desktop);
        $this->setSliderImgMobile($slider_img_mobile);
        $this->setSliderImgDesktop($slider_img_desktop);
        $this->setPageActusImgMobile($page_actus_img_mobile);
        $this->setPageActusImgDesktop($page_actus_img_desktop);
        $this->setMiseEnAvantHomepageImgMobile($mise_en_avant_homepage_img_mobile);
        $this->setMiseEnAvantHomepageImgDesktop($mise_en_avant_homepage_img_desktop);

        $this->setArticleTitle($article_title);
        $this->setDate($date);
        $this->setArticleTeaser($article_teaser);
        $this->setArticleContent($article_content);
        $this->setCtaTxt($cta_txt);
        $this->setCtaLink($cta_link);
        $this->setCategoryName($category_name);
        $this->setCategoryColor('');
    }

    public function jsonSerialize()
    {
        dump(get_object_vars($this));
        die;
        return get_object_vars($this);
    }

}

