<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo;

use System\Base\BaseModel;

class BasepackagesGeoTimezones extends BaseModel
{
    public $id;

    public $country_id;

    public $zone_name;

    public $gmt_offset;

    public $gmt_offset_name;

    public $abbreviation;

    public $tz_name;

    public $user_added;
}