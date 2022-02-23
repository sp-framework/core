<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Organisations\Model;

use System\Base\BaseModel;

class SystemApiXeroOrganisationsPhones extends BaseModel
{
    public $id;

    public $OrganisationID;

    public $PhoneType;

    public $PhoneNumber;

    public $PhoneCountryCode;
}