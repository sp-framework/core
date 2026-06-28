<?php

namespace System\Base\Providers\AccessServiceProvider\Model;

use System\Base\BaseModel;

class ServiceProviderAccessIpFilter extends BaseModel
{
    public $id;

    public $app_id;

    public $address_type;

    public $address;

    public $country_code;

    public $region_name;

    public $city_name;

    public $is_proxy;

    public $proxy_type;

    public $filter_type;

    public $parent_id;

    public $hit_count;

    public $incorrect_login_attempts;

    public $updated_by;

    public $updated_at;
}