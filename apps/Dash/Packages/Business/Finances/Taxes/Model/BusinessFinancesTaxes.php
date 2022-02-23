<?php

namespace Apps\Dash\Packages\Business\Finances\Taxes\Model;

use System\Base\BaseModel;

class BusinessFinancesTaxes extends BaseModel
{
    public $id;

    public $name;

    public $tax_group_id;

    public $amount;

    public $description;
}