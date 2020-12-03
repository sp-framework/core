<?php

namespace Applications\Core\Admin\Packages\Businesses\Model;

use System\Base\BaseModel;

class Businesses extends BaseModel
{
    public $id;

    public $name;

    public $abn;

    public $type;

    public $parent;

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