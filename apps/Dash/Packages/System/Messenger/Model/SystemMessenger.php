<?php

namespace Apps\Dash\Packages\System\Messenger\Model;

use System\Base\BaseModel;

class SystemMessenger extends BaseModel
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