<?php

namespace Apps\Dash\Packages\Business\Directory\Contacts\Model;

use Apps\Dash\Packages\Business\Directory\Vendors\Model\BusinessDirectoryVendors;
use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesActivityLogs;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesAddressBook;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesNotes;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersAccounts;

class BusinessDirectoryContacts extends BaseModel
{
    protected $modelRelations = [];

    public $id;

    public $portrait;

    public $account_id;

    public $account_email;

    public $vendor_id;

    public $first_name;

    public $last_name;

    public $full_name;

    public $job_title;

    public $contact_manager_id;

    public $contact_source;

    public $contact_source_details;

    public $contact_referrer_id;

    public $contact_phone;

    public $contact_phone_ext;

    public $contact_mobile;

    public $contact_fax;

    public $contact_secondary_email;

    public $cc_emails_to_secondary_email;

    public $contact_other;

    public $cc_details;

    public function initialize()
    {
        $this->modelRelations['account']['relationObj'] = $this->belongsTo(
            'id',
            BasepackagesUsersAccounts::class,
            'package_row_id',
            [
                'alias'                 => 'account',
                'params'                => [
                    'conditions'        => 'package_name = :package_name:',
                    'bind'              => [
                        'package_name'  => 'contacts'
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
                        'package_name'  => 'contacts'
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
                        'package_name'  => 'contacts'
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
                        'package_name'  => 'contacts'
                    ]
                ]
            ]
        );

        $this->modelRelations['vendor']['relationObj'] = $this->belongsTo(
            'vendor_id',
            BusinessDirectoryVendors::class,
            'id',
            [
                'alias' => 'vendor'
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