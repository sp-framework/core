<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Model;

use System\Base\BaseModel;

class SystemApiEbay extends BaseModel
{
    public $id;

    public $marketplace_id;

    public $use_systems_credentials;

    public $user_credentials_app_id;

    public $user_credentials_dev_id;

    public $user_credentials_cert_id;

    public $user_credentials_ru_name;

    public $user_credentials_scopes;

    public $app_access_token;

    public $app_access_token_valid_until;

    public $identifier;

    public $user_access_token;

    public $user_access_token_valid_until;

    public $refresh_token;

    public $refresh_token_valid_until;
}