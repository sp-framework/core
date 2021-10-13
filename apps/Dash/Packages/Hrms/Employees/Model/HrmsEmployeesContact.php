<?php

namespace Apps\Dash\Packages\Hrms\Employees\Model;

use Apps\Dash\Packages\Hrms\Employees\Model\HrmsEmployees;
use System\Base\BaseModel;

class HrmsEmployeesContact extends BaseModel
{
    protected $modelRelations = [];

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