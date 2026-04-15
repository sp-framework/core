<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model;

use System\Base\BaseModel;

class BasepackagesPages extends BaseModel
{
    public $id;

    public $name;

    public $description;

    public $app_type;

    public $visible_on_apps;

    public $content_source;

    public $html_file;

    public $html_code;
}