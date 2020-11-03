<?php

namespace Applications\Admin\Packages;

use Applications\Admin\Packages\Filters\Model\Filters as FiltersModel;
use System\Base\BasePackage;

class Filters extends BasePackage
{
    protected $modelToUse = FiltersModel::class;

    protected $packageName = 'filters';

    public $filters;


}