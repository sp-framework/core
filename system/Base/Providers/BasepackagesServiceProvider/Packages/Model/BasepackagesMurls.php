<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model;

use System\Base\BaseModel;

class BasepackagesMurls extends BaseModel
{
    public $id;

    public $app_id;

    public $domain_id;

    public $account_id;

    public $url;

    public $murl;

    public $hits;

    public $valid_till;
}