<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Model;

use System\Base\BaseModel;

class SystemApiXeroContactsAddresses extends BaseModel
{
    public $id;

    public $ContactID;

    public $AddressType;

    public $AddressLine1;

    public $AddressLine2;

    public $AddressLine3;

    public $AddressLine4;

    public $City;

    public $Region;

    public $PostCode;

    public $Country;

    public $AttentionTo;
}