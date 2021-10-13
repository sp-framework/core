<?php

namespace Apps\Dash\Packages\Hrms\Employees\Model;

use Apps\Dash\Packages\Hrms\Employees\Model\HrmsEmployees;
use System\Base\BaseModel;

class HrmsEmployeesEmployment extends BaseModel
{
    protected $modelRelations = [];

    public $id;

    public $employee_id;

    public $employment_type_id;

    public $contractor_vendor_id;

    public $hire_date;

    public $terminate_date;

    public $hire_manager_id;

    public $hire_referrer_id;

    public $employment_attachments;

    public $employment_notes;

    public function initialize()
    {
        $this->modelRelations['employee']['relationObj'] = $this->belongsTo(
            'employee_id',
            HrmsEmployees::class,
            'id',
            [
                'alias' => 'employee'
            ]
        );

        parent::initialize();
    }

    public function getModelRelations()
    {
        return $this->modelRelations;
    }
}