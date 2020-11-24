<?php

namespace System\Base\Providers\EmailServiceProvider\Model;

use System\Base\BaseModel;

class EmailServices extends BaseModel
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

    public $encryption;

    public $allow_html_body;
}