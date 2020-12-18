<?php

namespace Applications\Ecom\Dashboard\Packages\Channels\Model;

use System\Base\BaseModel;

class Channels extends BaseModel
{
    public $id;

    public $name;

    public $type;

    public $description;

    public $settings;
}