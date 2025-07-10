<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoRegions as GeoRegionsModel;

class GeoRegions extends BasePackage
{
    protected $modelToUse = GeoRegionsModel::class;

    protected $packageName = 'geoRegions';

    public $geoRegions;
}