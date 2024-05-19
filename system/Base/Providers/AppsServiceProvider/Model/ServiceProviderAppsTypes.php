<?php

namespace System\Base\Providers\AppsServiceProvider\Model;

use System\Base\BaseModel;

class ServiceProviderAppsTypes extends BaseModel
{
    public $id;

    public $name;

    public $app_type;

    public $description;

    public $version;

    public $repo;

    public $api_id;

    public $installed;

    public $update_available;

    public $update_version;

    public $updated_by;

    public $updated_on;

    public $level_of_update;

    public $auto_update;

    public $repo_details;
}