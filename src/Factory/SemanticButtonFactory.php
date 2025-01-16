<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\SemanticButton;
use Psr\Cache\CacheItemPoolInterface as AdapterInterface;

class SemanticButtonFactory extends AbstractFactory
{
    public function __construct(protected AdapterInterface $cache)
    {
        parent::__construct($cache);
    }

    public function create(array $data): SemanticButton
    {
        $semanticButton = new SemanticButton();
        $semanticButton->setId($data['id']);
        foreach ($data['dynamic_fields'] as $value) {
            $fieldName = $value['dynamic_field_configuration']['name']['default'];
            $fieldValue = $value['value'];
            switch ($fieldName) {
                case 'sem_btn_homepage_label':
                    $semanticButton->setLabel($fieldValue);
                    break;
                case 'sem_btn_homepage_search':
                    $semanticButton->setSearch($fieldValue);
                    break;
                case 'sem_btn_homepage_section_title':
                    $semanticButton->setSectionTitle($fieldValue);
                    break;
            }
        }

        return $semanticButton;
    }
}
