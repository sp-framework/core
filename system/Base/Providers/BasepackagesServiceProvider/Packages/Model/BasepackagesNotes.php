<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model;

use System\Base\BaseModel;

class BasepackagesNotes extends BaseModel
{
    public $id;

    public $note_type;

    public $note_app_visibility;

    public $account_id;

    public $is_private;

    public $created_at;

    public $package_name;

    public $package_row_id;

    public $note;

    public $note_attachments;
}