<?php

namespace Apps\Dash\Packages\Business\Directory\Vendors\Model;

use Apps\Dash\Packages\Business\Directory\Vendors\Model\BusinessDirectoryVendors;
use System\Base\BaseModel;

class BusinessDirectoryVendorsFinancialDetails extends BaseModel
{
    protected $modelRelations = [];

    public $id;

    public $vendor_id;

    public $acn;

    public $currency;

    public $bsb;

    public $account_number;

    public $swift_code;

    public $bills_due_date;

    public $bills_due_date_term;

    public $bills_discount;

    public $bills_tax_enabled;

    public $bills_tax_id;

    public $po_tax_enabled;

    public $po_tax_id;

    public $invoices_due_date;

    public $invoices_due_date_term;

    public $invoices_tax_enabled;

    public $invoices_tax_id;

    public $credit_limit_amount;

    public $credit_limit_block;

    public $invoice_discount;

    public function initialize()
    {
        $this->modelRelations['vendors']['relationObj'] = $this->belongsTo(
            'vendor_id',
            BusinessDirectoryVendors::class,
            'id',
            [
                'alias' => 'vendors'
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