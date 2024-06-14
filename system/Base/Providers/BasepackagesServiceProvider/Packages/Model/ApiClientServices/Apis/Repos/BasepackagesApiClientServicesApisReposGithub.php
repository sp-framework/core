<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\ApiClientServices\Apis\Repos;

use System\Base\BaseModel;

class BasepackagesApiClientServicesApisReposGithub extends BaseModel
{
    public $id;

    public $api_url;

    public $org_user;

    public $repo_url;

    public $branch;

    public $auth_type;

    public $username;

    public $password;

    public $access_token;

    public $authorization;

    public $sync;

    public function onConstruct()
    {
        $this->setSource('basepackages_api_client_services_apis_repos');
    }
}