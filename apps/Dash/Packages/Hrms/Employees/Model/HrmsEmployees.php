<?php

namespace Apps\Dash\Packages\Hrms\Employees\Model;

use Apps\Dash\Packages\Hrms\Employees\Model\HrmsEmployeesContact;
use Apps\Dash\Packages\Hrms\Employees\Model\HrmsEmployeesEmployment;
use Apps\Dash\Packages\Hrms\Employees\Model\HrmsEmployeesFinance;
use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesActivityLogs;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesAddressBook;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesNotes;
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
            'id',
            BasepackagesUsersAccounts::class,
            'package_row_id',
            [
                'alias'                 => 'account',
                'params'                => [
                    'conditions'        => 'package_name = :package_name:',
                    'bind'              => [
                        'package_name'  => 'employees'
                    ]
                ]
            ]
        );

        $this->modelRelations['addresses']['relationObj'] = $this->hasMany(
            'id',
            BasepackagesAddressBook::class,
            'package_row_id',
            [
                'alias'                 => 'addresses',
                'params'                => [
                    'conditions'        => 'package_name = :package_name:',
                    'bind'              => [
                        'package_name'  => 'employees'
                    ]
                ]
            ]
        );

        $this->modelRelations['notes']['relationObj'] = $this->hasMany(
            'id',
            BasepackagesNotes::class,
            'package_row_id',
            [
                'alias'                 => 'notes',
                'params'                => [
                    'conditions'        => 'package_name = :package_name:',
                    'bind'              => [
                        'package_name'  => 'employees'
                    ]
                ]
            ]
        );

        $this->modelRelations['activityLogs']['relationObj'] = $this->hasMany(
            'id',
            BasepackagesActivityLogs::class,
            'package_row_id',
            [
                'alias'                 => 'activityLogs',
                'params'                => [
                    'conditions'        => 'package_name = :package_name:',
                    'bind'              => [
                        'package_name'  => 'employees'
                    ]
                ]
            ]
        );

        parent::initialize();
    }

    public function getModelRelations()
    {
        if (count($this->modelRelations) === 0) {
            $this->initialize();
        }

        return $this->modelRelations;
    }
}