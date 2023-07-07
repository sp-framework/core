<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Api\Apis\Repos;

use System\Base\BaseModel;

class BasepackagesApiApisReposGitea extends BaseModel
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
        $this->setSource('basepackages_api_apis_repos');
    }
}