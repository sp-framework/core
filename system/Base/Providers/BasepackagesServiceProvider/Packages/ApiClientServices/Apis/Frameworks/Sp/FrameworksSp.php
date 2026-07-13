<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Frameworks\Sp;

use System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Frameworks\Frameworks;

class FrameworksSp extends Frameworks
{
    public function init($apiConfig = null, $api = null, $httpOptions = null, $monitorProgress = null)
    {
        if (!isset($apiConfig['category'])) {
            $apiConfig['category'] = 'Frameworks';
        }
        if (!isset($apiConfig['provider'])) {
            $apiConfig['provider'] = 'Sp';
        }

        parent::init($apiConfig, $api, $httpOptions, $monitorProgress);

        return $this;
    }
}