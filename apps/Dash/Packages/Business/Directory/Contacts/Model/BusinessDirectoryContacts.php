<?php

namespace Apps\Dash\Packages\Business\Directory\Contacts\Model;

use System\Base\BaseModel;

class BusinessDirectoryContacts extends BaseModel
{
    public $id;

    public $portrait;

    public $account_id;

    public $account_email;

    public $vendor_id;

    public $first_name;

    public $last_name;

    public $full_name;

    public $job_title;

    public $contact_manager_id;

    public $contact_source;

    public $contact_source_details;

    public $contact_referrer_id;

    public $contact_phone;

    public $contact_phone_ext;

    public $contact_mobile;

    public $contact_fax;

    public $contact_secondary_email;

    public $cc_emails_to_secondary_email;

    public $contact_other;

    public $contact_notes;

    public $address_ids;

    public $cc_details;
}