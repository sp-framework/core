<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model;

use System\Base\BaseModel;

class BasepackagesPages extends BaseModel
{
    public $id;

    public $name;

    public $description;

    public $content_source;

    public $visible_on_apps;

    public $html_file;

    public $html_code;
}