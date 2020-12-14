<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Storages;

use System\Base\BaseModel;

class Local extends BaseModel
{
    public $id;

    public $storages_id;

    public $uuid;

    public $signed_images;

    public $uuid_location;

    public $org_file_name;

    public $type;

    public $status;

    public $created_by;

    public $updated_by;

    public $created;

    public $updated;

    public function initialize()
    {
        $this->setSource('storages_local');
    }
}