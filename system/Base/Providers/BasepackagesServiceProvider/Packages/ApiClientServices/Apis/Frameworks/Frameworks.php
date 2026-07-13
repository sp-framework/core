<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Frameworks;

use System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis;

class Frameworks extends Apis
{
    public function init($apiConfig = null, $api = null, $httpOptions = null, $monitorProgress = null)
    {
        if (!isset($apiConfig['category'])) {
            $apiConfig['category'] = 'Frameworks';
        }

        parent::init($apiConfig, $api, $httpOptions, $monitorProgress);

        return $this;
    }
}