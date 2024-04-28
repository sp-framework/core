<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\ApiClientServices;

use System\Base\BaseModel;

class BasepackagesApiClientServices extends BaseModel
{
    public $id;

    public $api_category_id;

    public $name;

    public $category;

    public $provider;

    public $in_use;

    public $used_by;

    public $setup;

    public $location;

    public $description;
}