<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Messenger;

use System\Base\BaseModel;

class BasepackagesMessenger extends BaseModel
{
    public $id;

    public $from_account_id;

    public $to_account_id;

    public $message;

    public $read;

    public $edited;

    public $removed;

    public $created_at;

    public $updated_at;
}