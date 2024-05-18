<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Storages;

use System\Base\BaseModel;

class BasepackagesStoragesLocal extends BaseModel
{
    public $id;

    public $storages_id;

    public $uuid;

    public $uuid_location;

    public $links;

    public $org_file_name;

    public $size;

    public $type;

    public $is_pointer;

    public $orphan;

    public $created_by;

    public $updated_by;

    public $created;

    public $updated;
}