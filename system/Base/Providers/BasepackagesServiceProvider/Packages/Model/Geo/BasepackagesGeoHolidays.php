<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo;

use System\Base\BaseModel;

class BasepackagesGeoHolidays extends BaseModel
{
    public $id;

    public $name;

    public $date;

    public $state_id;

    public $is_regional_holiday;

    public $is_national_holiday;
}