<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Address;

use System\Base\BaseModel;

class BasepackagesAddressBook extends BaseModel
{
    public $id;

    public $name;

    public $address_type;

    public $is_primary;

    public $package_name;

    public $street_address;

    public $street_address_2;

    public $city_id;

    public $city_name;

    public $post_code;

    public $state_id;

    public $state_name;

    public $country_id;

    public $country_name;
}