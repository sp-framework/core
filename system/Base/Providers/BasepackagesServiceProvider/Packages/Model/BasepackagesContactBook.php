<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model;

use System\Base\BaseModel;

class BasepackagesContactBook extends BaseModel
{
    public $id;

    public $package_name;

    public $package_row_id;

    public $portrait;

    public $initials_avatar;

    public $prefix;

    public $first_name;

    public $last_name;

    public $suffix;

    public $full_name;

    public $contact_phone;

    public $contact_phone_ext;

    public $contact_mobile;

    public $contact_fax;

    public $email;

    public $secondary_email;

    public $cc_emails_to_secondary_email;

    public $contact_other;

    public $contact_notes;    
}