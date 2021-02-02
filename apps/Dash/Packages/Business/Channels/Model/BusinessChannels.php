<?php

namespace Apps\Dash\Packages\Business\Channels\Model;

use System\Base\BaseModel;

class BusinessChannels extends BaseModel
{
    public $id;

    public $name;

    public $type;

    public $description;

    public $settings;
}