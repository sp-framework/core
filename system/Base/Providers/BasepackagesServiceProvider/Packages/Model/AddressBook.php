<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model;

use System\Base\BaseModel;

class AddressBook extends BaseModel
{
    public $id;

    public $name;

    public $package_name;

    public $street_address;

    public $street_address_2;

    public $city_id;

    public $city_name;

    public $pin_code;

    public $state_id;

    public $state_name;

    public $country_id;

    public $country_name;
}