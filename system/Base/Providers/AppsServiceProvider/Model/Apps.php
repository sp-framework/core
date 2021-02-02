<?php

namespace System\Base\Providers\AppsServiceProvider\Model;

use System\Base\BaseModel;

class Apps extends BaseModel
{
    public $id;

    public $name;

    public $route;

    public $description;

    public $app_type;

    public $default_component;

    public $errors_component;

    public $registration_allowed;

    public $registration_role_id;

    public $guest_role_id;

    public $can_login_role_ids;

    public $settings;
}