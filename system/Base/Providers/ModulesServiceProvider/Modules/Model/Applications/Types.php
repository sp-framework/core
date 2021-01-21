<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model\Applications;

use System\Base\BaseModel;

class Types extends BaseModel
{
    public $id;

    public $app_type;

    public $name;

    public $description;

    public function initialize()
    {
        $this->setSource('application_types');
    }
}