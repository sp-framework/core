<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

class Applications extends BaseModel
{
    public $id;

    public $name;

    public $route;

    public $description;

    public $category;

    public $sub_category;

    public $default_component;

    public $errors_component;

    public $registration_allowed;

    public $registration_role_id;

    public $guest_role_id;

    public $can_login_role_ids;

    public $settings;
}