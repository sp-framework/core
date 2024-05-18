<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Dashboards;

use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesDashboards;

class BasepackagesDashboardsWidgets extends BaseModel
{
    protected $modelRelations = [];

    public $id;

    public $widget_id;

    public $dashboard_id;

    public $settings;

    public $sequence;

    public function initialize()
    {
        $this->modelRelations['dashboards']['relationObj'] = $this->belongsTo(
            'dashboard_id',
            BasepackagesDashboards::class,
            'id'
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