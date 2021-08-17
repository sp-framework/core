<?php

namespace Apps\Dash\Packages\Hrms\Employees\Model;

use System\Base\BaseModel;

class HrmsEmployeesFinance extends BaseModel
{
    public $id;

    public $employee_id;

    public $tfn;

    public $currency;

    public $bsb;

    public $account_number;

    public $swift_code;
}