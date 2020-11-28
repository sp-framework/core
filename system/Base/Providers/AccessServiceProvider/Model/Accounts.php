<?php

namespace System\Base\Providers\AccessServiceProvider\Model;

use System\Base\BaseModel;

class Accounts extends BaseModel
{
    public $id;

    public $email;

    public $password;

    public $permissions;

    public $can_login;

    public $version;

    public $remember_token;

    public $remember_identifier;
    // public $id;
    // public $customer_name;
    // public $contact_last_name;
    // public $contact_first_name;
    // public $phone;
    // public $address_line_1;
    // public $address_line_2;
    // public $city;
    // public $state;
    // public $post_code;
    // public $country;
    // public $sales_rep_employee_number;
    // public $credit_limit;

    public function initialize()
    {
        // $this->setSource('customers');
    }
}
