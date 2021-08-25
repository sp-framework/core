<?php

namespace Apps\Dash\Packages\Business\Directory\Vendors\Model;

use Apps\Dash\Packages\Business\Directory\Vendors\Model\BusinessDirectoryVendorsFinancialDetails;
use System\Base\BaseModel;

class BusinessDirectoryVendors extends BaseModel
{
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

    public $contact_ids;

    public $address_ids;

    public function initialize()
    {
        $this->hasOne(
            'id',
            BusinessDirectoryVendorsFinancialDetails::class,
            'vendor_id',
            [
                'alias' => 'financial_details'
            ]
        );
    }
}