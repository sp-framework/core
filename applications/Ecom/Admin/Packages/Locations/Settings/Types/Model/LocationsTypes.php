<?php

namespace Applications\Ecom\Admin\Packages\Locations\Settings\Types\Model;

use System\Base\BaseModel;

class LocationsTypes extends BaseModel
{
    public $id;

    public $name;

    public $description;

    public function initialize()
    {
        $this->setSource('locations_types');
        $this->useDynamicUpdate(true);
    }
}