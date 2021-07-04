<?php

namespace Apps\Dash\Packages\Business\Finances\Taxes;

use Apps\Dash\Packages\Business\Finances\Taxes\Model\BusinessTaxes;
use System\Base\BasePackage;

class Taxes extends BasePackage
{
    protected $modelToUse = BusinessTaxes::class;

    protected $packageName = 'taxes';

    public $taxes;
}