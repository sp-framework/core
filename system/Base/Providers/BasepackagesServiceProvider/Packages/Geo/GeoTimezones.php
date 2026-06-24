<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Geo;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoTimezones as GeoTimezonesModel;

class GeoTimezones extends BasePackage
{
    protected $modelToUse = GeoTimezonesModel::class;

    protected $packageName = 'geoTimezones';

    public $geoTimezones;

    public function init(bool $resetCache = false)
    {
        if ($this->opCache) {
            if (!$resetCache && $this->opCache->checkCache('geoTimezones', 'core')) {
                $this->geoTimezones = $this->opCache->getCache('geoTimezones', 'core');
            } else {
                $this->getAll($resetCache);

                $this->opCache->setCache('geoTimezones', $this->geoTimezones, 'core');
            }
        } else {
            $this->getAll($resetCache);
        }

        return $this;
    }
}