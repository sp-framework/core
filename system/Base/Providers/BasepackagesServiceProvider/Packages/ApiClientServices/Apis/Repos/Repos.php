<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos;

use System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis;

class Repos extends Apis
{
    public function init($apiConfig = null, $api = null, $httpOptions = null)
    {
        parent::init($apiConfig, $api, $httpOptions);

        return $this;
    }
}