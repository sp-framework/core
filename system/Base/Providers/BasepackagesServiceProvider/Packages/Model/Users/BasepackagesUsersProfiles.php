<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users;

use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersAccounts;

class BasepackagesUsersProfiles extends BaseModel
{
    public $id;

    public $portrait;

    public $account_id;

    public $first_name;

    public $last_name;

    public $full_name;

    public $contact_address_id;

    public $contact_phone;

    public $contact_phone_ext;

    public $contact_mobile;

    public $contact_fax;

    public $contact_secondary_email;

    public $cc_emails_to_secondary_email;

    public $contact_other;

    public $contact_notes;

    public function initialize()
    {
        $this->hasOne(
            'account_id',
            BasepackagesUsersAccounts::class,
            'id',
            [
                'alias' => 'account'
            ]
        );
    }
}