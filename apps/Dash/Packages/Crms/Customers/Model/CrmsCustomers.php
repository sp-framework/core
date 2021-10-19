<?php

namespace Apps\Dash\Packages\Crms\Customers\Model;

use Apps\Dash\Packages\Crms\Customers\Model\CrmsCustomersFinancialDetails;
use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesActivityLogs;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesAddressBook;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesNotes;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersAccounts;

class CrmsCustomers extends BaseModel
{
    protected $modelRelations = [];

    public $id;

    public $portrait;

    public $account_id;

    public $account_email;

    public $customer_group_id;

    public $first_name;

    public $last_name;

    public $full_name;

    public $contact_source;

    public $contact_source_details;

    public $contact_referrer_id;

    public $contact_phone;

    public $contact_phone_ext;

    public $contact_mobile;

    public $contact_secondary_email;

    public $cc_emails_to_secondary_email;

    public $contact_other;

    public $address_ids;

    public function initialize()
    {
        $this->modelRelations['financial_details']['relationObj'] = $this->hasOne(
            'id',
            CrmsCustomersFinancialDetails::class,
            'customer_id',
            [
                'alias' => 'financial_details'
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

        $this->modelRelations['addresses']['relationObj'] = $this->hasMany(
            'id',
            BasepackagesAddressBook::class,
            'package_row_id',
            [
                'alias'                 => 'addresses',
                'params'                => [
                    'conditions'        => 'package_name = :package_name:',
                    'bind'              => [
                        'package_name'  => 'customers'
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
                        'package_name'  => 'customers'
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
                        'package_name'  => 'customers'
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