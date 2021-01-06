<?php

namespace Applications\Dash\Packages\Locations\Model;

use System\Base\BaseModel;

class Locations extends BaseModel
{
    public $id;

    public $name;

    public $inbound_shipping;

    public $outbound_shipping;

    public $can_stock;

    public $total_stock_qty;

    public $address_id;

    public $notes;
}