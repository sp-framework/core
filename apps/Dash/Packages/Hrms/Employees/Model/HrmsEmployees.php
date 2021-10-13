<?php

namespace Apps\Dash\Packages\Hrms\Employees\Model;

use Apps\Dash\Packages\Hrms\Employees\Model\HrmsEmployeesContact;
use Apps\Dash\Packages\Hrms\Employees\Model\HrmsEmployeesEmployment;
use Apps\Dash\Packages\Hrms\Employees\Model\HrmsEmployeesFinance;
use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersAccounts;

class HrmsEmployees extends BaseModel
{
    protected $modelRelations = [];

    public $id;

    public $ref_id;

    public $entity_id;

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
        $this->modelRelations['employment']['relationObj'] = $this->hasOne(
            'id',
            HrmsEmployeesEmployment::class,
            'employee_id',
            [
                'alias' => 'employment'
            ]
        );

        $this->modelRelations['contact']['relationObj'] = $this->hasOne(
            'id',
            HrmsEmployeesContact::class,
            'employee_id',
            [
                'alias' => 'contact'
            ]
        );

        $this->modelRelations['finance']['relationObj'] = $this->hasOne(
            'id',
            HrmsEmployeesFinance::class,
            'employee_id',
            [
                'alias' => 'finance'
            ]
        );

        $this->modelRelations['account']['relationObj'] = $this->belongsTo(
            'account_id',
            BasepackagesUsersAccounts::class,
            'id',
            [
                'alias' => 'account'
            ]
        );

        parent::initialize();
    }

    public function getModelRelations()
    {
        return $this->modelRelations;
    }
}