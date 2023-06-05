<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\ExpertContentBanner;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Service\Attribute\Required;

class UpplerDynamicEntityService extends HttpClientProvider
{

    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public EntityManagerInterface $em;

    public function getDynamicsEntitiesCategories(array $filters = []): Array
    {
        $filters =  empty($filters) ? [] : $filters;

        $urlFilters = null;

        if (!empty($filters)) {
            foreach ($filters as $filter) {
                $urlFilters.= null === $urlFilters ? '?expand[]=' . $filter : '&expand[]=' . $filter;
            }
        }

        $res = $this->request(
            'GET',
            $this->apiUrl . 'v1/administrator/dynamic-field-configuration' . $urlFilters, [],true
        );

        if (Response::HTTP_OK === $res->getStatusCode()) {
            $dynamicsEntities = json_decode($res->getContent(), true);
        } else {
            throw new NotFoundHttpException('Aucun champs dynamique trouvé');
        }

        return $dynamicsEntities;
    }

    public function getDynamicsEntities(array $expands = [], array $criterias = []): Array
    {
        $expands =  empty($expands) ? [] : $expands;

        $options = null;

        if (!empty($expands)) {
            foreach ($expands as $expand) {
                $options.= null === $options ? '?expand[]=' . $expand : '&expand[]=' . $expand;
            }
        }
        if (!empty($criterias)) {
            foreach ($criterias as $criteria=>$value) {
                $options.= (null === $options)
                    ? ('?criteria['.$criteria.']=' . $value)
                    : ('&criteria['.$criteria.']=' . $value)
                ;
            }
        }

        $res = $this->request(
            'GET',
            $this->apiUrl . 'v1/administrator/dynamic-entity' . $options, [],true
        );

        if (Response::HTTP_OK === $res->getStatusCode()) {
            $dynamicsEntities = json_decode($res->getContent(), true);
        } else {
            throw new NotFoundHttpException('Aucune entité dynamique trouvée');
        }

        return $dynamicsEntities;
    }

    public function getDynamicEntityBanner(): ?ExpertContentBanner
    {
        $res = $this->request(
            'GET',
            $this->apiUrl . 'v1/administrator/dynamic-entity?expand[]=dynamic_fields&criteria[dynamic_entity_configuration_id]=2&criteria[enabled]=1',
            [],
            true
        );

        if (Response::HTTP_OK === $res->getStatusCode()) {
            $remoteBanner = json_decode($res->getContent(), true);
            if (!empty($remoteBanner[0])) {
                $banner = new ExpertContentBanner();
                $banner->setId($remoteBanner[0]['id']);
                $banner->setSlug($remoteBanner[0]['slug']);
                foreach ($remoteBanner[0]['dynamic_fields'] as $value) {
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
                return $banner;
            } else {
                throw new NotFoundHttpException('Aucune bannière trouvée');
            }
        } else {
            throw new NotFoundHttpException('Aucune bannière trouvée');
        }
    }

}
