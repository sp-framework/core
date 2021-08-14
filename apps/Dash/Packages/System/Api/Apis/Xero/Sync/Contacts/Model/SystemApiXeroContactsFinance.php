<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Model;

use System\Base\BaseModel;

class SystemApiXeroContactsFinance extends BaseModel
{
    public $id;

    public $ContactID;

    public $BankAccountDetails;

    public $TaxNumber;

    public $AccountsReceivableTaxType;

    public $AccountsPayableTaxType;

    public $SalesDefaultAccountCode;

    public $PurchaseDefaultAccountCode;

    public $DefaultCurrency;

    public $Discount;

    public $BatchPayments;

    public $PaymentTermsBillsDay;

    public $PaymentTermsBillsType;

    public $PaymentTermsSalesDay;

    public $PaymentTermsSalesType;

    public $SalesTrackingCategories;

    public $PurchasesTrackingCategories;
}