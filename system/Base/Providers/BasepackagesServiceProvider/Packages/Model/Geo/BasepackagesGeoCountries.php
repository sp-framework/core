<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo;

use System\Base\BaseModel;

class BasepackagesGeoCountries extends BaseModel
{
    public $id;

    public $name;

    public $native;

    public $nationality;

    public $capital;

    public $iso2;

    public $iso3;

    public $currency;

    public $currency_name;

    public $currency_symbol;

    public $currency_enabled;

    public $region_id;

    public $region;

    public $subregion_id;

    public $subregion;

    public $numeric_code;

    public $phone_code;

    public $tld;

    public $emoji;

    public $emojiU;

    public $longitude;

    public $latitude;

    public $translations;

    public $installed;

    public $enabled;

    public $user_added;
}