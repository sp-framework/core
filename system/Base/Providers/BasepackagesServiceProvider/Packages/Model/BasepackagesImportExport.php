<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model;

use System\Base\BaseModel;

class BasepackagesImportExport extends BaseModel
{
    public $id;

    public $type;

    public $status;

    public $package_name;

    public $app_id;

    public $account_id;

    public $email_to;

    public $job_id;

    public $file;

    public $logs;
}