<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\ApiClientServices\Apis\Repos;

use System\Base\BaseModel;

class BasepackagesApiClientServicesApisReposGitea extends BaseModel
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

    public $refresh_token;

    public $authorization;

    public $sync;

    public function init($app = null)
    {
        $this->setTableSource('basepackages_api_client_services_apis_repos');

        return $this;
    }
}