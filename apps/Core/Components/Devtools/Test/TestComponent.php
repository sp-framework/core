<?php

namespace Apps\Core\Components\Devtools\Test;

use Apps\Core\Packages\Devtools\DicExtractData\DevtoolsDicExtractData;
use System\Base\BaseComponent;

class TestComponent extends BaseComponent
{
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        // $install = new \Apps\Fintech\Components\Dashboards\Install\Install;

        // $install->init()->install();
        // $adminComponents = $this->basepackages->utils->scanDir('apps/Core/Components/', true);

        // foreach ($adminComponents['files'] as $adminComponentKey => $adminComponent) {
        //     if (strpos($adminComponent, 'component.json')) {
        //         try {
        //             $jsonFile =
        //                 $this->helper->decode(
        //                     $this->localContent->read($adminComponent),
        //                     true
        //                 );
        //         } catch (\throwable $e) {
        //             throw new \Exception($e->getMessage() . '. Problem reading component.json at location ' . $adminComponent);
        //         }

        //         if ($jsonFile['menu'] && $jsonFile['menu'] !== 'false') {
        //             $this->basepackages->menus->addMenu($jsonFile);
        //         }
        //     }
        // }
    }

    /**
     * @api_acl(name=view)
     */
    public function apiViewAction()
    {
        $this->addResponse('Test', 0, ['connection' => $this->connection->getId(), 'session' => $this->session->getId()]);
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