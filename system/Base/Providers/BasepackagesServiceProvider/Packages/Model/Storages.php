<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model;

use System\Base\BaseModel;

class Storages extends BaseModel
{
    public $id;

    public $name;

    public $type;

    public $description;

    public $permission;

    public $allowed_image_mime_types;

    public $allowed_image_sizes;

    public $images_path;

    public $cache_path;

    public $max_image_size;

    public $default_image_quality;

    public $max_image_file_size;

    public $allowed_file_mime_types;

    public $data_path;

    public $max_data_file_size;
}