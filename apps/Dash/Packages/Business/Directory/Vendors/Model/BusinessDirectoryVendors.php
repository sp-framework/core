<?php

namespace Apps\Dash\Packages\Business\Directory\Vendors\Model;

use Apps\Dash\Packages\Business\Directory\Contacts\Model\BusinessDirectoryContacts;
use Apps\Dash\Packages\Business\Directory\Vendors\Model\BusinessDirectoryVendorsFinancialDetails;
use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesActivityLogs;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesAddressBook;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesNotes;

class BusinessDirectoryVendors extends BaseModel
{
    protected $modelRelations = [];

    public $id;

    public $logo;

    public $abn;

    public $business_name;

    public $vendor_group_id;

    public $is_manufacturer;

    public $is_supplier;

    public $does_dropship;

    public $is_service_provider;

    public $does_jobwork;

    public $is_b2b_customer;

    public $b2b_account_managers;

    public $brands;

    public $product_count;

    public $contact_phone;

    public $contact_phone_ext;

    public $contact_fax;

    public $contact_other;

    public $website;

    public $email;

    public function initialize()
    {
        $this->modelRelations['financial_details']['relationObj'] = $this->hasOne(
            'id',
            BusinessDirectoryVendorsFinancialDetails::class,
            'vendor_id',
            [
                'alias' => 'financial_details'
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
                        'package_name'  => 'vendors'
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
                        'package_name'  => 'vendors'
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
                        'package_name'  => 'vendors'
                    ]
                ]
            ]
        );

        $this->modelRelations['contacts']['relationObj'] = $this->hasMany(
            'id',
            BusinessDirectoryContacts::class,
            'vendor_id',
            [
                'alias' => 'contacts'
            ]
        );

        parent::initialize();
    }

    public function getModelRelations()
    {
        return $this->modelRelations;
    }
}