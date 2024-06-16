<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use System\Base\BasePackage;

class Maintenance extends BasePackage
{
    //protected $modelToUse = ::class;

    protected $packageName = 'maintenance';

    public $maintenance;

    public function getMaintenanceById($id)
    {
        $maintenance = $this->getById($id);

        if ($maintenance) {
            //
            $this->addResponse('Success');

            return;
        }

        $this->addResponse('Error', 1);
    }

    public function addMaintenance($data)
    {
        //Maintenances can only be added via Core app and by Admin group users.
    }

    public function updateMaintenance($data)
    {
        $maintenance = $this->getById($id);

        if ($maintenance) {
            //
            $this->addResponse('Success');

            return;
        }

        $this->addResponse('Error', 1);
    }

    public function removeMaintenance($data)
    {
        $maintenance = $this->getById($id);

        if ($maintenance) {
            //
            $this->addResponse('Success');

            return;
        }

        $this->addResponse('Error', 1);
    }
}