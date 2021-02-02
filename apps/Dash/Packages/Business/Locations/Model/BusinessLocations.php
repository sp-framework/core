<?php

namespace Apps\Dash\Packages\Business\Locations\Model;

use System\Base\BaseModel;

class BusinessLocations extends BaseModel
{
    public $id;

    public $name;

    public $inbound_shipping;

    public $outbound_shipping;

    public $can_stock;

    public $total_stock_qty;

    public $total_employees;

    public $user_profile_ids;

    public $address_id;

    public $notes;
}