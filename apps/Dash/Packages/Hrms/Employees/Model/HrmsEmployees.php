<?php

namespace Apps\Dash\Packages\Hrms\Employees\Model;

use Apps\Dash\Packages\Hrms\Employees\Model\HrmsEmployeesContact;
use Apps\Dash\Packages\Hrms\Employees\Model\HrmsEmployeesEmployment;
use Apps\Dash\Packages\Hrms\Employees\Model\HrmsEmployeesFinance;
use System\Base\BaseModel;

class HrmsEmployees extends BaseModel
{
    public $id;

    public $portrait;

    public $account_id;

    public $account_email;

    public $status;

    public $first_name;

    public $last_name;

    public $full_name;

    public $designation;

    public $manager_id;

    public function initialize()
    {
        $this->hasOne(
            'id',
            HrmsEmployeesEmployment::class,
            'employee_id',
            [
                'alias' => 'employment'
            ]
        );

        $this->hasOne(
            'id',
            HrmsEmployeesContact::class,
            'employee_id',
            [
                'alias' => 'contact'
            ]
        );

        $this->hasOne(
            'id',
            HrmsEmployeesFinance::class,
            'employee_id',
            [
                'alias' => 'finance'
            ]
        );
    }
}