<?php

namespace Apps\Dash\Packages\Business\Directory\Vendors\Model;

use System\Base\BaseModel;

class BusinessDirectoryVendorsFinancialDetails extends BaseModel
{
    public $id;

    public $vendor_id;

    public $acn;

    public $currency;

    public $bsb;

    public $account_number;

    public $swift_code;

    public $bills_due_date;

    public $bills_due_date_term;

    public $bills_tax_enabled;

    public $bills_tax_id;

    public $invoices_due_date;

    public $invoices_due_date_term;

    public $invoices_tax_enabled;

    public $invoices_tax_id;

    public $credit_limit_amount;

    public $credit_limit_block;

    public $discount;
}