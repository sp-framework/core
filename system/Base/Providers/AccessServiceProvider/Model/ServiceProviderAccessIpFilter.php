<?php

namespace System\Base\Providers\AccessServiceProvider\Model;

use System\Base\BaseModel;

class ServiceProviderAccessIpFilter extends BaseModel
{
    public $id;

    public $app_id;

    public $ip_address;

    public $address_type;//1 - Host, 2 - Network

    public $filter_type;//1 - Allow, 2 - Block, 3 - Monitor (for failed login attempts)

    public $added_by;//0 - Auth_Service, account_id

    public $hit_count;//Hit count on filter_type 1 or 2

    public $incorrect_attempts;//for filter_type 3

    public $updated_at;//for auto unblock
}