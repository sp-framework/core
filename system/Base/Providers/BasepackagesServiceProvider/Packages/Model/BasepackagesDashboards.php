<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model;

use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Dashboards\BasepackagesDashboardsWidgets;

class BasepackagesDashboards extends BaseModel
{
    protected $modelRelations = [];

    public $id;

    public $name;

    public $app_id;

    public $app_default;

    public $created_by;

    public $shared;

    public $is_default;

    public $settings;

    public function initialize()
    {
        $this->modelRelations['widgets']['relationObj'] = $this->hasMany(
            'id',
            BasepackagesDashboardsWidgets::class,
            'dashboard_id',
            [
                'alias'         => 'widgets'
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