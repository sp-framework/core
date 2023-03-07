<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Model;

use System\Base\BaseModel;

class SystemApiXeroContactsPhones extends BaseModel
{
    public $id;

    public $ContactID;

    public $PhoneType;

    public $PhoneNumber;

    public $PhoneAreaCode;

    public $PhoneCountryCode;
}