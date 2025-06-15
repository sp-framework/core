<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoHolidays as GeoHolidaysModel;

class GeoHolidays extends BasePackage
{
    protected $modelToUse = GeoHolidaysModel::class;

    protected $packageName = 'geoHolidays';

    public $geoHolidays;
}