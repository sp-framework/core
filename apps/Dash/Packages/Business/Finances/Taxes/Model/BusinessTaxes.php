<?php

namespace Apps\Dash\Packages\Business\Finances\Taxes\Model;

use System\Base\BaseModel;

class BusinessTaxes extends BaseModel
{
    public $id;

    public $name;

    public $conditions;

    public $component_id;

    public $permission;

    public $is_default;

    public $shared_ids;
}