<?php

namespace System\Base\Providers\AppsServiceProvider\Apps;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\AppsServiceProvider\Model\Apps\AppsTypes;

class Types extends BasePackage
{
    protected $modelToUse = AppsTypes::class;

    protected $packageName = 'types';

    public $types;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        return $this;
    }
}