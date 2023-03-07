<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Organisations\Model;

use System\Base\BaseModel;

class SystemApiXeroOrganisations extends BaseModel
{
    public $id;

    public $baz_entity_id;

    public $api_id;

    public $OrganisationID;

    public $APIKey;

    public $Name;

    public $LegalName;

    public $PaysTax;

    public $Version;

    public $OrganisationType;

    public $CountryCode;

    public $IsDemoCompany;

    public $OrganisationStatus;

    public $RegistrationNumber;

    public $PeriodLockDate;

    public $CreatedDateUTC;

    public $Timezone;

    public $OrganisationEntityType;

    public $ShortCode;

    public $Class;

    public $Edition;

    public $ExternalLinks;

    public $resync_local;

    public $resync_remote;

    public $conflict;

    public $conflict_id;
}