<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Applications;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Applications\Types as TypesModel;

class Types extends BasePackage
{
    protected $modelToUse = TypesModel::class;

    protected $packageName = 'applicationsTypes';

    public $applicationsTypes;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        return $this;
    }
}