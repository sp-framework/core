<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo;

use System\Base\BaseModel;

class GeoCities extends BaseModel
{
    public $id;

    public $name;

    public $latitude;

    public $longitude;

    // public $post_code;

    public $state_id;

    public $country_id;

    public $user_added;
}