<?php

namespace Apps\Dash\Packages\Crms\Customers\Model;

use Apps\Dash\Packages\Crms\Customers\Model\CrmsCustomers;
use System\Base\BaseModel;

class CrmsCustomersFinancialDetails extends BaseModel
{
    protected $modelRelations = [];

    public $id;

    public $customer_id;

    public $abn;

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

    public function initialize()
    {
        $this->modelRelations['customers']['relationObj'] = $this->belongsTo(
            'customer_id',
            CrmsCustomers::class,
            'id',
            [
                'alias' => 'customers'
            ]
        );

        parent::initialize();
    }

    public function getModelRelations()
    {
        if (count($this->modelRelations) === 0) {
            $this->initialize();
        }

        return $this->modelRelations;
    }
}