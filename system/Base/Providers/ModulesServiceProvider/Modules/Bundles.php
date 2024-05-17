<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesBundles;

class Bundles extends BasePackage
{
    protected $modelToUse = ModulesBundles::class;

    public $bundles;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        return $this;
    }

    public function getBundleByRepo($repo)
    {
        foreach($this->bundles as $bundle) {
            if ($bundle['repo'] == $repo) {
                return $bundle;
            }
        }

        return false;
    }

    public function getBundleById($id)
    {
        foreach($this->bundles as $bundle) {
            if ($bundle['id'] == $id) {
                return $bundle;
            }
        }

        return false;
    }

    public function getBundlesByApiId($apiId)
    {
        $bundles = [];

        foreach($this->bundles as $bundle) {
            if ($bundle['api_id'] == $apiId) {
                array_push($bundles, $bundle);
            }
        }

        return $bundles;
    }
}