<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea;

use System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Repos;

class ApisReposGitea extends Repos
{
    public function init($apiConfig = null, $api = null, $httpOptions = null, $monitorProgress = null)
    {
        if (!isset($apiConfig['category'])) {
            $apiConfig['category'] = 'Repos';
        }
        if (!isset($apiConfig['provider'])) {
            $apiConfig['provider'] = 'Gitea';
        }

        parent::init($apiConfig, $api, $httpOptions, $monitorProgress);

        return $this;
    }
}