<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model;

use System\Base\BaseModel;

class BasepackagesTags extends BaseModel
{
    public $id;

    public $name;

    public $description;

    public $swatch;

    public $package_name;

    public $package_row_ids;
}