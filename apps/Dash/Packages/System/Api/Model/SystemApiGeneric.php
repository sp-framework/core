<?php

namespace Apps\Dash\Packages\System\Api\Model;

use System\Base\BaseModel;

class SystemApiGeneric extends BaseModel
{
    public $id;

    public $api_url;

    public $auth_token;

    public $username;

    public $password;

    public $authorization;

    public $token;
}