<?php

namespace System\Base\Providers\AccessServiceProvider\Model;

use System\Base\BaseModel;

class ServiceProviderAccessIpFilters extends BaseModel
{
    public $id;

    public $app_id;

    public $address_type;

    public $address;

    public $ip2location_proxy;

    public $decimal;

    public $filter_type;

    public $parent_id;

    public $hit_count;

    public $incorrect_login_attempts;

    public $updated_by;

    public $updated_at;
}