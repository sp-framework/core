<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesExternals;

class Externals extends BasePackage
{
    protected $modelToUse = ModulesExternals::class;

    public $externals;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        return $this;
    }

    public function getExternalById($id)
    {
        foreach($this->externals as $external) {
            if ($external['id'] == $id) {
                return $external;
            }
        }

        return false;
    }

    public function getExternalByName($name)
    {
        foreach($this->externals as $external) {
            if ($external['name'] == $name) {
                return $external;
            }
        }

        return false;
    }
}