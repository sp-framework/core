<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea;

use System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Repos;

class ApisReposGitea extends Repos
{
    public function init($apiConfig = null, $api = null, $httpOptions = null)
    {
        parent::init($apiConfig, $api, $httpOptions);

        return $this;
    }
}