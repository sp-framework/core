<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Model;

use System\Base\BaseModel;

class SystemApiXeroContacts extends BaseModel
{
    public $id;

    public $baz_vendor_id;

    public $api_id;

    public $ContactID;

    public $AccountNumber;

    public $ContactStatus;

    public $Name;

    public $FirstName;

    public $LastName;

    public $Website;

    public $EmailAddress;

    public $ContactGroups;

    public $IsSupplier;

    public $IsCustomer;

    public $HasAttachments;

    public $HasValidationErrors;

    public $UpdatedDateUTC;

    public $BrandingTheme;

    public $resync_local;

    public $resync_remote;

    public $conflict;

    public $conflict_id;
}