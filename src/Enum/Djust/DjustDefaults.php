<?php

declare(strict_types=1);

namespace App\Enum\Djust;

enum DjustDefaults: string
{
    case STORE = 'default_store';
    case CURRENCY = 'EUR';
    case LOCALE = 'fr-FR';
    case COUNTRY_CODE = 'FR';

    //    TODO: Il faudrait que les constantes SHOP_ID_TYPE et ID_TYPE_DJUST soient dans une enum à part (DjustIdType)
    case ID_TYPE_DJUST = 'DJUST_ID';
    case SHOP_ID_TYPE = 'ID';

    case SEARCH_PAGE_NUMBER = '0';
    case SEARCH_PER_PAGE_ACCORD_CADRE = '100';
    case SEARCH_PER_PAGE_PRODUCT = '36';
}
