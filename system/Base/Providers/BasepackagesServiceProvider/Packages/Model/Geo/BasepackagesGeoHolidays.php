<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo;

use System\Base\BaseModel;

class BasepackagesGeoHolidays extends BaseModel
{
    public $id;

    public $name;

    public $date;

    public $is_national_holiday;

    public $country_id;

    public $state_id;
}