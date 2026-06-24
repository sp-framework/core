<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo;

use System\Base\BaseModel;

class BasepackagesGeoPostcodes extends BaseModel
{
    public $id;

    public $code;

    public $name;

    public $city_id;

    public $state_id;

    public $country_id;
}