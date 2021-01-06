<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ApplicationTypes as ApplicationTypesModel;

class ApplicationTypes extends BasePackage
{
    protected $modelToUse = ApplicationTypesModel::class;

    protected $packageName = 'applicationTypes';

    public $applicationTypes;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        return $this;
    }
}