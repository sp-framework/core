<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Model;

use System\Base\BaseModel;

class SystemApiXeroContacts extends BaseModel
{
    public $id;

    public $baz_vendor_id;

    public $api_id;

    public $ContactID;

    public $ContactStatus;

    public $Name;

    public $FirstName;

    public $LastName;

    public $DefaultCurrency;

    public $UpdatedDateUTC;

    public $HasValidationErrors;

    public $resync_local;

    public $resync_remote;

    public $conflict;

    public $conflict_id;
}