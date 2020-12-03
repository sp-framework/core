<?php

namespace Applications\Ecom\Admin\Packages\Locations\Model;

use System\Base\BaseModel;

class Locations extends BaseModel
{
    public $id;

    public $name;

    public $type;

    public $description;

    public $primary_contact;

    public $secondary_contact;

    public $inbound_shipping;

    public $outbound_shipping;

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