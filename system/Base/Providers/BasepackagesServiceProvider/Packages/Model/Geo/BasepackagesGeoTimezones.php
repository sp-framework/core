<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo;

use System\Base\BaseModel;

class BasepackagesGeoTimezones extends BaseModel
{
    public $id;

    public $zone_name;

    public $tz_name;

    public $gmt_offset;

    public $gmt_offset_name;

    public $gmt_offset_dst;

    public $gmt_offset_name_dst;

    public $abbreviation;

    public $user_added;
}