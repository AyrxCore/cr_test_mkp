<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\DynamicEntity;
use App\Dto\Seller;
use App\Dto\Price;
use App\Dto\Product;
use App\Dto\Property;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Service\Attribute\Required;

class UpplerDynamicEntityService extends HttpClientProvider
{

    private const DEFAULT_IMG = '/vuejs/assets/img/default-image.png';
    private string $upplerUrlSourceProductImg;
    private string $upplerUrlSourceSellerImg;
    public function __construct(
        string $env,
        string $apiUrl,
        string $adminClientId,
        string $adminClientSecret,
        string $adminTokenFile,
        string $httpCachePath,
        string $upplerUrlSourceProductImg,
        string $upplerUrlSourceSellerImg
    )
    {
        parent::__construct($env, $apiUrl, $adminClientId, $adminClientSecret, $adminTokenFile, $httpCachePath);
        $this->upplerUrlSourceProductImg = $upplerUrlSourceProductImg;
        $this->upplerUrlSourceSellerImg = $upplerUrlSourceSellerImg;
    }

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


    public function getDynamicEntity(int $productId = null, array $filters = []): Array
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
            $this->apiUrl . 'v1/administrator/dynamic-entity/' . $productId . $urlFilters, [],true
        );

        if (Response::HTTP_OK === $res->getStatusCode()) {
            dump(json_decode($res->getContent(), true));
            die;
            return $this->populateProduct(json_decode($res->getContent()));
        } else {
            throw new NotFoundHttpException('Aucune entité dynamique trouvée');
        }

        return [];
    }


}
