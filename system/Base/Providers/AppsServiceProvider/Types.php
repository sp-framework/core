<?php

namespace System\Base\Providers\AppsServiceProvider;

use System\Base\BasePackage;
use System\Base\Providers\AppsServiceProvider\Model\ServiceProviderAppsTypes;

class Types extends BasePackage
{
    protected $modelToUse = ServiceProviderAppsTypes::class;

    protected $packageName = 'types';

    public $types;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        return $this;
    }
}