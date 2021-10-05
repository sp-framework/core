<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo;

use System\Base\BaseModel;

class BasepackagesGeoStates extends BaseModel
{
    public $id;

    public $name;

    public $state_code;

    public $latitude;

    public $longitude;

    public $country_id;

    public $user_added;
}