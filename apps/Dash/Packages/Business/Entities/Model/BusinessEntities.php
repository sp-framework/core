<?php

namespace Apps\Dash\Packages\Business\Entities\Model;

use System\Base\BaseModel;

class BusinessEntities extends BaseModel
{
    public $id;

    public $logo;

    public $abn;

    public $name;

    public $entity_type;

    public $address_id;

    public $api_id;
}