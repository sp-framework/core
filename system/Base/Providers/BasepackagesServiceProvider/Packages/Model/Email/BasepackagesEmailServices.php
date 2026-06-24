<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Email;

use System\Base\BaseModel;

class BasepackagesEmailServices extends BaseModel
{
    public $id;

    public $name;

    public $description;

    public $host;

    public $port;

    public $auth;

    public $username;

    public $password;

    public $from_address;

    public $from_name;

    public $encryption;

    public $allow_html_body;
}