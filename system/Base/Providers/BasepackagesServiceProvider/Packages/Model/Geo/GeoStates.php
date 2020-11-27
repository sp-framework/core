<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo;

use System\Base\BaseModel;

class GeoStates extends BaseModel
{
    public $id;

    public $name;

    public $state_code;

    public $country_id;

    public $user_added;
}