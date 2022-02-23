<?php

namespace Apps\Dash\Packages\System\Api\Model;

use System\Base\BaseModel;

class SystemApi extends BaseModel
{
    public $id;

    public $api_id;

    public $name;

    public $api_type;

    public $in_use;

    public $used_by;

    public $setup;

    public $description;
}