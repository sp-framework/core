<?php

namespace Apps\Dash\Packages\Hrms\Statuses\Model;

use System\Base\BaseModel;

class HrmsStatuses extends BaseModel
{
    public $id;

    public $name;

    public $description;

    public $employees_count;
}