<?php

namespace Apps\Dash\Packages\Business\Channels\Model;

use System\Base\BaseModel;

class BusinessChannels extends BaseModel
{
    public $id;

    public $channel_id;

    public $name;

    public $channel_type;

    public $description;

    public $order_count;

    public $product_count;
}