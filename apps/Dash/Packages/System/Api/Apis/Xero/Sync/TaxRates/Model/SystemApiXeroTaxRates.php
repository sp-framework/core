<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\TaxRates\Model;

use System\Base\BaseModel;

class SystemApiXeroTaxRates extends BaseModel
{
    public $id;

    public $baz_tax_group_id;

    public $api_id;

    public $Name;

    public $TaxType;

    public $CanApplyToAssets;

    public $CanApplyToEquity;

    public $CanApplyToExpenses;

    public $CanApplyToLiabilities;

    public $CanApplyToRevenue;

    public $DisplayTaxRate;

    public $EffectiveRate;

    public $Status;

    public $TaxComponents;

    public $resync_local;

    public $resync_remote;

    public $conflict;

    public $conflict_id;
}