<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Model;

use System\Base\BaseModel;

class SystemApiXeroContactsContactPersons extends BaseModel
{
    public $id;

    public $baz_contact_id;

    public $ContactID;

    public $FirstName;

    public $LastName;

    public $EmailAddress;

    public $IncludeInEmails;

    public $resync_local;

    public $resync_remote;

    public $conflict;

    public $conflict_id;
}