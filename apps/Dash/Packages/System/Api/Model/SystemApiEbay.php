<?php

namespace Apps\Dash\Packages\System\Api\Model;

use System\Base\BaseModel;

class SystemApiEbay extends BaseModel
{
    public $id;

    public $app_access_token;

    public $app_access_token_valid_until;

    public $session_id;

    public $user_access_token;

    public $user_access_token_valid_until;

    public $refresh_token;

    public $refresh_token_valid_until;
}