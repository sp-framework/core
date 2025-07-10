<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo;

use System\Base\BaseModel;

class BasepackagesGeoRegions extends BaseModel
{
    public $id;

    public $name;

    public $parent_region_id;
}