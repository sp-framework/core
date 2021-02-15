<?php

namespace Apps\Dash\Packages\Business\Directory\Contacts\Model;

use System\Base\BaseModel;

class BusinessDirectoryContacts extends BaseModel
{
    public $id;

    public $portrait;

    public $account_id;

    public $first_name;

    public $last_name;

    public $full_name;

    public $contact_address_ids;

    public $contact_phone;

    public $contact_phone_ext;

    public $contact_mobile;

    public $contact_fax;

    public $contact_other;

    public $contact_notes;
}