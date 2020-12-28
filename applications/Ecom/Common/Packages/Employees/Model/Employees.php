<?php

namespace Applications\Ecom\Common\Packages\Employees\Model;

use System\Base\BaseModel;

class Employees extends BaseModel
{
    public $id;

    public $image;

    public $employee_id;

    public $account_id;

    public $status;

    public $first_name;

    public $middle_name;

    public $last_name;

    public $full_name;

    public $landline;

    public $landline_ext;

    public $mobile;

    public $hire_date;

    public $terminate_date;

    public $addresses;

    public $notes;
}