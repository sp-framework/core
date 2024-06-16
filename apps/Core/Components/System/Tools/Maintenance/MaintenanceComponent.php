<?php

namespace Apps\Core\Components\System\Tools\Maintenance;

use System\Base\BaseComponent;

class MaintenanceComponent extends BaseComponent
{
    protected $maintenancePackage;

    public function initialize()
    {
        $this->maintenancePackage = $this->usePackage('maintenance');
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        //Visibility of a maintenance can only be seen by admin users. Rest of the users can only see specific fields.
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $maintenance = $this->maintenancePackage->getById($this->getData()['id']);

                if (!$maintenance) {
                    return $this->throwIdNotFound();
                }

                $this->view->maintenance = $maintenance;
            }

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'system/tools/maintenances',
                    'remove'    => 'system/tools/maintenances/remove'
                ]
            ];

        $this->generateDTContent(
            $this->maintenancePackage,
            'system/tools/maintenances/view',
            null,
            ['maintenance'],
            false,
            ['maintenance'],
            $controlActions,
            null,
            null,
            'maintenance'
        );

        $this->view->pick('maintenances/list');
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        $this->requestIsPost();

        //$this->package->add{?}($this->postData());

        $this->addResponse(
            $this->package->packagesData->responseMessage,
            $this->package->packagesData->responseCode
        );
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        $this->requestIsPost();

        //$this->package->update{?}($this->postData());

        $this->addResponse(
            $this->package->packagesData->responseMessage,
            $this->package->packagesData->responseCode
        );
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        $this->requestIsPost();

        //$this->package->remove{?}($this->postData());

        $this->addResponse(
            $this->package->packagesData->responseMessage,
            $this->package->packagesData->responseCode
        );
    }
}