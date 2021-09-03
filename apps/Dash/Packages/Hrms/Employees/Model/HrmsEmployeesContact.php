<?php

namespace Apps\Dash\Packages\Hrms\Employees\Model;

use System\Base\BaseModel;

class HrmsEmployeesContact extends BaseModel
{
    public $id;

    public $employee_id;

    public $work_type_id;

    public $contact_location_id;

    public $contact_address_id;

    public $contact_phone;

    public $contact_phone_ext;

    public $contact_mobile;

    public $contact_fax;

    public $contact_other;
}