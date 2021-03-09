<?php

namespace Apps\Dash\Packages\Business\Directory\Vendors\Model;

use System\Base\BaseModel;

class BusinessDirectoryVendors extends BaseModel
{
    public $id;

    public $logo;

    public $abn;

    public $name;

    public $is_manufacturer;

    public $does_dropship;

    public $does_jobwork;

    public $brands;

    public $product_count;

    public $primary_contact_id;

    public $other_contact_ids;

    public $primary_address_id;

    public $other_address_id;

    public $token;

    public $internal_notes;
}