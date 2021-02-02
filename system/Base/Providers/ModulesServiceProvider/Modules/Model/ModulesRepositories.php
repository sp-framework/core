<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

class ModulesRepositories extends BaseModel
{
    public $id;

    public $name;

    public $description;

    public $repo_url;

    public $site_url;

    public $branch;

    public $auth_token;

    public $username;

    public $password;

    public $token;
}