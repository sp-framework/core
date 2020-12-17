<?php

namespace Applications\Ecom\Dashboard\Packages\Inventory\Suppliers\Model;

use System\Base\BaseModel;

class Suppliers extends BaseModel
{
    public $id;

    public $logo;

    public $abn;

    public $name;

    public $type;

    public $is_manufacturer;

    public $does_dropship;

    public $contact_first_name;

    public $contact_last_name;

    public $contact_phone;

    public $contact_email;

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