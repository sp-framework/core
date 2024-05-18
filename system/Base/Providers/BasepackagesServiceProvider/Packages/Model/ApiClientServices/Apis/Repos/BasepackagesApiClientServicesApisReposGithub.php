<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\ApiClientServices\Apis\Repos;

use System\Base\BaseModel;

class BasepackagesApiClientServicesApisReposGithub extends BaseModel
{
    public $id;

    public $repo_url;

    public $site_url;

    public $branch;

    public $auth_type;

    public $authorization;

    public $username;

    public $password;

    public $token;

    public function onConstruct()
    {
        $this->setSource('basepackages_api_client_services_apis_repos');
    }
}