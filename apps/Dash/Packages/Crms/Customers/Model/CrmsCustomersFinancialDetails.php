<?php

namespace Apps\Dash\Packages\Crms\Customers\Model;

use System\Base\BaseModel;

class CrmsCustomersFinancialDetails extends BaseModel
{
    public $id;

    public $customer_id;

    public $acn;

    public $currency;

    public $bsb;

    public $account_number;

    public $swift_code;

    public $invoices_due_day;

    public $invoices_due_day_term;

    public $invoices_tax_enabled;

    public $invoices_tax_id;

    public $credit_limit_amount;

    public $credit_limit_block;

    public $invoice_discount;

    public $cc_details;
}