<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

class ModulesViewsSettings extends BaseModel
{
    public $id;

    public $view_id;

    public $domain_id;

    public $app_id;

    public $settings;
}