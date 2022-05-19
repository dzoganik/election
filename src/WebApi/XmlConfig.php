<?php

declare(strict_types=1);

namespace App\WebApi;

/**
 * Class XmlConfig
 * @package App\WebApi
 */
class XmlConfig
{
    public const RESULTS_URL = 'https://volby.cz/pls/ps2021/vysledky';

    public const REGION_TITLE_FIELD = 'title';
    public const REGION_PARTIES_FIELD = 'parties';

    public const PARTY_NUMBER_FIELD = 'number';
    public const PARTY_TITLE_FIELD = 'title';
    public const PARTY_PERCENT_FIELD = 'percent';

    public const ELEMENT_REGION_TITLE = 'NAZ_KRAJ';
    public const ELEMENT_PARTY = 'STRANA';
    public const ELEMENT_PARTY_VALUES = 'HODNOTY_STRANA';
    public const ELEMENT_PARTY_NUMBER = 'KSTRANA';
    public const ELEMENT_PARTY_TITLE = 'NAZ_STR';
    public const ELEMENT_PARTY_PERCENT = 'PROC_HLASU';
    public const ELEMENT_RESULTS = 'VYSLEDKY';
    public const ELEMENT_REGION_CR = 'CR';
}
