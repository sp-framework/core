<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Organisations\Model;

use System\Base\BaseModel;

class SystemApiXeroOrganisationsAddresses extends BaseModel
{
    public $id;

    public $OrganisationID;

    public $AddressType;

    public $AddressLine1;

    public $AddressLine2;

    public $City;

    public $PostalCode;

    public $Country;

    public $AttentionTo;
}