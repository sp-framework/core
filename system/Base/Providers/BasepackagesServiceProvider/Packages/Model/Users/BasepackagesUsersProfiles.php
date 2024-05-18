<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users;

use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesAddressBook;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersAccounts;

class BasepackagesUsersProfiles extends BaseModel
{
    protected $modelRelations = [];

    public $id;

    public $account_id;

    public $portrait;

    public $initials_avatar;

    public $first_name;

    public $last_name;

    public $full_name;

    public $contact_address_id;

    public $contact_phone;

    public $contact_phone_ext;

    public $contact_mobile;

    public $contact_fax;

    public $secondary_email;

    public $cc_emails_to_secondary_email;

    public $contact_other;

    public $contact_notes;

    public $settings;

    public function initialize()
    {
        $this->modelRelations['account']['relationObj'] = $this->belongsTo(
            'account_id',
            BasepackagesUsersAccounts::class,
            'id',
            [
                'alias' => 'account'
            ]
        );

        $this->modelRelations['address']['relationObj'] = $this->hasOne(
            'id',
            BasepackagesAddressBook::class,
            'package_row_id',
            [
                'alias'                 => 'address',
                'params'                => [
                    'conditions'        => 'package_name = :package_name:',
                    'bind'              => [
                        'package_name'  => 'profile'
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