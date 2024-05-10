<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github;

use System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Repos;

class ApisReposGithub extends Repos
{
    public function init($apiConfig = null, $api = null, $httpOptions = null)
    {
        if (!isset($apiConfig['category'])) {
            $apiConfig['category'] = 'Repos';
        }
        if (!isset($apiConfig['provider'])) {
            $apiConfig['provider'] = 'Github';
        }

        parent::init($apiConfig, $api, $httpOptions);

        return $this;
    }
}