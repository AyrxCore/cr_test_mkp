<?php

declare(strict_types=1);

namespace App\Constants;

class PartnerStoresExcelColumnIndices
{
    public const COL_STORE_NAME = 0;
    public const COL_ADDRESS = 1;
    public const COL_ADDRESS_COMPLEMENT = 2;
    public const COL_POSTAL_CODE = 3;
    public const COL_CITY = 4;
    public const COL_PHONE = 5;
    public const COL_LATITUDE = 6;
    public const COL_LONGITUDE = 7;
    public const COL_ACCORD_ID = 8;

    public const REQUIRED_HEADERS = [
        self::COL_STORE_NAME => "Nom de l'agence",
        self::COL_ADDRESS => 'Adresse',
        self::COL_ADDRESS_COMPLEMENT => 'Adresse complémentaire',
        self::COL_POSTAL_CODE => 'CP',
        self::COL_CITY => 'Ville',
        self::COL_PHONE => 'Téléphone (+33…)',
        self::COL_LATITUDE => 'Latitude',
        self::COL_LONGITUDE => 'Longitude',
        self::COL_ACCORD_ID => 'Accord ID',
    ];
}
