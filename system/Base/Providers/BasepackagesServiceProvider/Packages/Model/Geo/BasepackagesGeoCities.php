<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo;

use System\Base\BaseModel;

class BasepackagesGeoCities extends BaseModel
{
    public $id;

    public $name;

    public $latitude;

    public $longitude;

    public $state_id;

    public $country_id;
}