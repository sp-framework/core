<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo;

use System\Base\BaseModel;

class GeoCountries extends BaseModel
{
    public $id;

    public $name;

    public $iso3;

    public $iso2;

    public $phone_code;

    public $capital;

    public $currency;

    public $native;

    public $region;

    public $subregion;

    public $emoji;

    public $emojiU;

    public $user_added;
}