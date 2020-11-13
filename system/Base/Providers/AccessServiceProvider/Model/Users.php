<?php

namespace System\Base\Providers\AccessServiceProvider\Model;

use System\Base\BaseModel;

class Users extends BaseModel
{
    /**
     * @Primary
     * @Identity
     * @Column(type='integer', nullable=false)
     */
    public $id;

    /**
     * @Column(column="email", type="string", length=50)
     */
    public $email;

    /**
     * @Column(column="password", type="string", length=50)
     */
    public $password;

    /**
     * @Column(column="can_login", type="string", length=50)
     */
    public $can_login;

    /**
     * @Column(column="version", type="string", length=15)
     */
    public $version;

    /**
     * @Column(column="remember_token", type="string", length=2048, nullable=true)
     */
    public $remember_token;

    /**
     * @Column(column="remember_identifier", type="string", length=2048, nullable=true)
     */
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
