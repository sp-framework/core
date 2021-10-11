<?php

namespace Apps\Dash\Packages\Business\Directory\Vendors\Model;

use Apps\Dash\Packages\Business\Directory\Vendors\Model\BusinessDirectoryVendorsFinancialDetails;
use System\Base\BaseModel;

class BusinessDirectoryVendors extends BaseModel
{
    protected static $modelRelations = [];

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
        self::$modelRelations['financial_details']['relationObj'] = $this->hasOne(
            'id',
            BusinessDirectoryVendorsFinancialDetails::class,
            'vendor_id',
            [
                'alias' => 'financial_details'
            ]
        );

        self::$modelRelations['addresses']['relationObj'] = $this->hasOne(
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

        parent::initialize();
    }

    public function getModelRelations()
    {
        return self::$modelRelations;
    }
}