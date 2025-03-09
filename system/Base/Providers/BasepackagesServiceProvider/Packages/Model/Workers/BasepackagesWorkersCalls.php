<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers;

use System\Base\BaseModel;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesPackages;

class BasepackagesWorkersCalls extends BaseModel
{
    protected $modelRelations = [];

    public $id;

    public $name;

    public $display_name;

    public $description;

    public $class;

    public $can_be_scheduled;

    public $can_be_run_on_demand;

    public $package_id;

    public function initialize()
    {
        $this->modelRelations['package']['relationObj'] = $this->hasOne(
            'package_id',
            ModulesPackages::class,
            'id',
            [
                'alias'         => 'package'
            ]
        );

        parent::initialize();
    }

    public function getModelRelations()
    {
        if (count($this->modelRelations) === 0) {
            $this->initialize();
        }

        return $this->modelRelations;
    }
}