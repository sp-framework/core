<?php

namespace Apps\Dash\Packages\System\Api\Apis\Simplify\Model;

use System\Base\BaseModel;

class SystemApiSimplify extends BaseModel
{
    public $id;

    public $use_systems_credentials;

    public $user_credentials_client_id;

    public $user_credentials_client_secret;

    public $user_credentials_redirect_uri;

    public $user_credentials_scopes;

    public $user_id_token;

    public $identifier;

    public $user_access_token;

    public $user_access_token_valid_until;

    public $refresh_token;

    public $refresh_token_valid_until;
}