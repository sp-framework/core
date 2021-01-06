<?php

namespace Applications\Dash\Packages\Ims\Specifications\Model;

use System\Base\BaseModel;

class Specifications extends BaseModel
{
    public $id;

    public $name;

    public $is_group;

    public $group_id;

    public $description;

    public $product_count;
}