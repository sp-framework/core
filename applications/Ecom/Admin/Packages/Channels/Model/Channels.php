<?php

namespace Applications\Ecom\Admin\Packages\Channels\Model;

use System\Base\BaseModel;

class Channels extends BaseModel
{
    public $id;

    public $name;

    public $type;

    public $description;

    public $settings;
}