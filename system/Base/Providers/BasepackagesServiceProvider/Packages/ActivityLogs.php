<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesActivityLogs;

class ActivityLogs extends BasePackage
{
    protected $modelToUse = BasepackagesActivityLogs::class;

    protected $packageNameS = 'activitylogs';

    public $activityLogs;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        return $this;
    }


}