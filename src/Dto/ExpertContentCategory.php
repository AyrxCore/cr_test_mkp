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
                'summary' => 'Liste des categories de contenus experts',
                'description' => 'Permet de récupérer les categories des contenus experts'
            ],
            'path' => '/experts_contents_categories',
            'method' => 'GET',
            'normalization' => false,
        ]
    ],
    itemOperations: [
        'get' => [
            'path' => '/expert_content_categorie/{id}',
            'requirements' => ['id' => '\d+']
        ],
    ],
    formats: ['json'],
)]
class ExpertContentCategory implements \JsonSerializable
{
    #[ApiProperty(identifier: true)]
    private ?int $id = null;
    private string $name = '';
    private string $color = '';

    /**
     * @param  int|null  $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param  string  $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param  string  $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    public function hydrate($dynamicEntity){
        $id = $dynamicEntity['id'];
        $slug = $dynamicEntity['slug'];
        $is_enable = $dynamicEntity['is_enabled']==="true";

        //            $categorie_id = $dynamicEntity['dynamic_entity_configuration']['id'];
        //            $categorie_name = $dynamicEntity['dynamic_entity_configuration']['name'];
        //            $date = new \DateTime($dynamicEntity['created_at']);
        //            $date = new \DateTime($dynamicEntity['updated_at']);

        foreach ($dynamicEntity['dynamic_fields'] as $dynamicField){
            //                $fieldName = $dynamicField['dynamic_field_configuration']['name']['default'];
            $fieldName = $dynamicField['dynamic_field_configuration']['name']['fr'];

            switch ($fieldName){
                case 'slider_img_desktop':
                    if(count($dynamicField['images'])===1){
                        $slider_img_desktop = 'https://uppler-platform-quantis.s3.eu-west-3.amazonaws.com/uppler_medium/'.$dynamicField['images'][0]['path'];
                    }else{
                        throw new NotFoundHttpException('slider_img_desktop img is empty');
                    }
                    break;

                case 'slider_img_mobile':
                    if(count($dynamicField['images'])===1){
                        $slider_img_mobile = 'https://uppler-platform-quantis.s3.eu-west-3.amazonaws.com/uppler_medium/'.$dynamicField['images'][0]['path'];
                    }else{
                        throw new NotFoundHttpException('slider_img_mobile img is empty');
                    }
                    break;

                case 'article_img_desktop':
                    if(count($dynamicField['images'])===1){
                        $article_img_desktop = 'https://uppler-platform-quantis.s3.eu-west-3.amazonaws.com/uppler_medium/'.$dynamicField['images'][0]['path'];
                    }else{
                        throw new NotFoundHttpException('article_img_desktop img is empty');
                    }
                    break;

                case 'article_img_mobile':
                    if(count($dynamicField['images'])===1){
                        $article_img_mobile = 'https://uppler-platform-quantis.s3.eu-west-3.amazonaws.com/uppler_medium/'.$dynamicField['images'][0]['path'];
                    }else{
                        throw new NotFoundHttpException('article_img_mobile img is empty');
                    }
                    break;

                case 'article_title':
                    if(empty($dynamicField['value'])){
                        throw new NotFoundHttpException('article_title value is empty');
                    }else{
                        $article_title=$dynamicField['value'];
                    }
                    break;

                case 'date':
                    if(empty($dynamicField['value'])){
                        throw new NotFoundHttpException('date value is empty');
                    }else{
                        $date = new \DateTime($dynamicField['value']);
                    }
                    break;

                case 'article_teaser':
                    if(empty($dynamicField['value'])){
                        throw new NotFoundHttpException('article_teaser value is empty');
                    }else{
                        $article_teaser=$dynamicField['value'];
                    }
                    break;

                case 'article_content':
                    if(empty($dynamicField['value'])){
                        throw new NotFoundHttpException('article_content value is empty');
                    }else{
                        $article_content=$dynamicField['value'];
                    }
                    break;

                case 'cta_txt':
                    if(empty($dynamicField['value'])){
                        $cta_txt='';
                    }else{
                        $cta_txt=$dynamicField['value'];
                    }
                    break;

                case 'cta_link':
                    if(empty($dynamicField['value'])){
                        $cta_link='';
                    }else{
                        $cta_link=$dynamicField['value'];
                    }
                    break;

                case 'category_name':
                    if(empty($dynamicField['choices'][0]['value'])){
                        throw new NotFoundHttpException('category_name value is empty');
                    }else{
                        $category_name=$dynamicField['choices'][0]['value'];
                    }
                    break;

                case 'category_color':
                    if(empty($dynamicField['choices'][0]['value'])){
                        throw new NotFoundHttpException('category_color value is empty');
                    }else{
                        $category_color=$dynamicField['choices'][0]['value'];
                    }
                    break;

            }
        }

        $article_img_desktop = 'https://uppler-platform-quantis.s3.eu-west-3.amazonaws.com/uppler_medium/9d/a6/5f62d7dca175ed9cf773d7702b95.jpg';
        $article_img_mobile = 'https://uppler-platform-quantis.s3.eu-west-3.amazonaws.com/uppler_medium/17/70/118858f171a17f070369208a66c7.jpg';
        $slider_img_desktop = 'https://uppler-platform-quantis.s3.eu-west-3.amazonaws.com/uppler_medium/9c/90/85c7d061f3af10abbbfcd483743e.png';
        $slider_img_mobile = 'https://uppler-platform-quantis.s3.eu-west-3.amazonaws.com/uppler_medium/13/ae/e25a7efb7c19ef4182c5b135d3e8.png';

        $article_img_desktop = $slider_img_desktop;

        $this->setId($id);
        $this->setSliderImgDesktop($slider_img_desktop);
        $this->setSliderImgMobile($slider_img_mobile);
        $this->setArticleImgDesktop($article_img_desktop);
        $this->setArticleImgMobile($article_img_mobile);
        $this->setArticleTitle($article_title);
        $this->setDate($date);
        $this->setArticleTeaser($article_teaser);
        $this->setArticleContent($article_content);
        $this->setCtaTxt($cta_txt);
        $this->setCtaLink($cta_link);
        $this->setCategoryName($category_name);
        $this->setCategoryColor($category_color);


    }

    public function jsonSerialize()
    {
        return  get_object_vars($this);
    }
}

