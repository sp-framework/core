<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoTimezones as GeoTimezonesModel;

class GeoTimezones extends BasePackage
{
    protected $modelToUse = GeoTimezonesModel::class;

    protected $packageName = 'geoTimezones';

    public $geoTimezones;
}