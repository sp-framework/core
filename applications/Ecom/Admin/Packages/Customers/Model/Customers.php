<?php

namespace Applications\Ecom\Admin\Packages\Customers\Model;

use System\Base\BaseModel;

class Customers extends BaseModel
{
    public $id;

    public $logo;

    public $name;

    public $description;

    public $product_count;
}