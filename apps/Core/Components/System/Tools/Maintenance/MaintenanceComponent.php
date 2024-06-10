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
        trace([$this->maintenancePackage], true, false, true);
        return;
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