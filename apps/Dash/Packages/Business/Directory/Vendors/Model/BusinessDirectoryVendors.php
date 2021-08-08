<?php

namespace Apps\Dash\Packages\Business\Directory\Vendors\Model;

use System\Base\BaseModel;

class BusinessDirectoryVendors extends BaseModel
{
    public $id;

    public $logo;

    public $abn;

    public $business_name;

    public $is_manufacturer;

    public $is_supplier;

    public $does_dropship;

    public $is_service_provider;

    public $does_jobwork;

    public $brands;

    public $vendor_group_id;

    public $product_count;

    public $contact_ids;

    public $address_ids;

    public $acn;

    public $currency;

    public $bsb;

    public $account_number;

    public $swift_code;
}