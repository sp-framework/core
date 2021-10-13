<?php

namespace Apps\Dash\Packages\Crms\Customers\Model;

use Apps\Dash\Packages\Crms\Customers\Model\CrmsCustomersFinancialDetails;
use System\Base\BaseModel;
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

        parent::initialize();
    }

    public function getModelRelations()
    {
        return $this->modelRelations;
    }
}