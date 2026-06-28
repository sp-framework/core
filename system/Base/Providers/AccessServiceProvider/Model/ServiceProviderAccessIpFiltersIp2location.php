<?php

namespace System\Base\Providers\AccessServiceProvider\Model;

use System\Base\BaseModel;

class ServiceProviderAccessIpFiltersIp2location extends BaseModel
{
    public $id;

    public $address;

    public $decimal;

    public $country_code;

    public $region_name;

    public $city_name;

    public $is_proxy;

    public $proxy_type;
}