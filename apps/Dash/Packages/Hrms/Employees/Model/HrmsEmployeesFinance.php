<?php

namespace Apps\Dash\Packages\Hrms\Employees\Model;

use Apps\Dash\Packages\Hrms\Employees\Model\HrmsEmployees;
use System\Base\BaseModel;

class HrmsEmployeesFinance extends BaseModel
{
    protected static $modelRelations = [];

    public $id;

    public $employee_id;

    public $tfn;

    public $currency;

    public $bsb;

    public $account_number;

    public $swift_code;

    public function initialize()
    {
        self::$modelRelations['employee']['relationObj'] = $this->belongsTo(
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
        return self::$modelRelations;
    }
}