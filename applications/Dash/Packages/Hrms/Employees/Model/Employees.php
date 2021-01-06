<?php

namespace Applications\Dash\Packages\Hrms\Employees\Model;

use System\Base\BaseModel;

class Employees extends BaseModel
{
    public $id;

    public $image;

    public $account_id;

    public $status;

    public $first_name;

    public $last_name;

    public $full_name;

    public $designation;

    public $manager_id;

    public $type_id;

    public $work_type_id;

    public $hire_date;

    public $terminate_date;

    public $hire_manager_id;

    public $hire_referrer_id;

    public $employment_attachments;

    public $employment_notes;

    public $contact_work;

    public $contact_work_ext;

    public $contact_mobile;

    public $contact_fax;

    public $contact_other;

    public $contact_notes;

    public $contact_location_id;

    public $contact_address_id;

    public $skills;

    public $skills_attachments;

    public $additional_notes;

    public function initialize()
    {
        $this->setSource('hrms_employees');

        $this->useDynamicUpdate(true);
    }
}