<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoRegions as GeoRegionsModel;

class GeoRegions extends BasePackage
{
    protected $modelToUse = GeoRegionsModel::class;

    protected $packageName = 'geoRegions';

    public $geoRegions;

    public function init(bool $resetCache = false)
    {
        if ($this->opCache) {
            if (!$resetCache && $this->opCache->checkCache('geoRegions', 'core')) {
                $this->geoRegions = $this->opCache->getCache('geoRegions', 'core');
            } else {
                $this->getAll($resetCache);

                $this->opCache->setCache('geoRegions', $this->geoRegions, 'core');
            }
        } else {
            $this->getAll($resetCache);
        }

        return $this;
    }
}