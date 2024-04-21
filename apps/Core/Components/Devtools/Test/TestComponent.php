<?php

namespace Apps\Core\Components\Devtools\Test;

use System\Base\BaseComponent;

class TestComponent extends BaseComponent
{
    protected $sourceDir = 'system/Base/Providers/BasepackagesServiceProvider/Packages/Geo/Data/';

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        //
    }

    /**
     * @api_acl(name=view)
     */
    public function apiViewAction()
    {
        sleep(5);
        $this->addResponse('Test', 0, ['view' => true]);
    }

    public function addAction()
    {
        //
    }

    /**
     * @api_acl(name=add)
     */
    public function apiAddAction()
    {
        $this->addResponse('Test', 0, ['add' => true]);
    }

    public function updateAction()
    {
        //
    }

    /**
     * @api_acl(name=update)
     */
    public function apiUpdateAction()
    {
        $this->addResponse('Test', 0, ['update' => true]);
    }

    public function removeAction()
    {
        //
    }

    /**
     * @api_acl(name=remove)
     */
    public function apiRemoveAction()
    {
        $this->addResponse('Test', 0, ['remove' => true]);
    }
}