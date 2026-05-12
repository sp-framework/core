<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model;

use System\Base\BaseModel;

class BasepackagesAddressBook extends BaseModel
{
    public $id;

    public $package_name;

    public $package_row_id;

    public $address_reference;

    public $attention_to;

    public $street_address;

    public $street_address_2;

    public $street_address_3;

    public $street_address_4;

    public $city_id;

    public $city_name;

    public $post_code;

    public $state_id;

    public $state_name;

    public $country_id;

    public $country_name;
}