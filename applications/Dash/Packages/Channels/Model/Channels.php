<?php

namespace Applications\Dash\Packages\Channels\Model;

use System\Base\BaseModel;

class Channels extends BaseModel
{
    public $id;

    public $name;

    public $type;

    public $description;

    public $settings;
}