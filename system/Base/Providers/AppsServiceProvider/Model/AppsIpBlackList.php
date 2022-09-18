<?php

namespace System\Base\Providers\AppsServiceProvider\Model;

use System\Base\BaseModel;
use System\Base\Providers\AppsServiceProvider\Model\Apps;

class AppsIpBlackList extends BaseModel
{
    public $id;

    public $app_id;

    public $ip_address;

    public $invalid_attempts;
}