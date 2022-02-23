<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Organisations\Model;

use System\Base\BaseModel;

class SystemApiXeroOrganisationsFinance extends BaseModel
{
    public $id;

    public $OrganisationID;

    public $BaseCurrency;

    public $TaxNumber;

    public $FinancialYearEndDay;

    public $FinancialYearEndMonth;

    public $SalesTaxBasis;

    public $SalesTaxPeriod;

    public $DefaultSalesTax;

    public $DefaultPurchasesTax;

    public $PaymentTermsBillsDay;

    public $PaymentTermsBillsType;

    public $PaymentTermsSalesDay;

    public $PaymentTermsSalesType;
}