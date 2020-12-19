<?php

namespace Applications\Ecom\Dashboard\Packages\Inventory\Brands\Model;

use System\Base\BaseModel;

class Brands extends BaseModel
{
    public $id;

    public $logo;

    public $name;

    public $description;

    public $product_count;
}