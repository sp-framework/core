<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesProcesses;

class Processes extends BasePackage
{
    protected $modelToUse = BasepackagesProcesses::class;

    protected $packageName = 'processes';

    public $processes;

    public function init(bool $resetCache = false)
    {
        return $this;
    }
}