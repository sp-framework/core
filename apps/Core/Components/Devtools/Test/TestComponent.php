<?php

namespace Apps\Core\Components\Devtools\Test;

use Apps\Core\Packages\Devtools\DicExtractData\DevtoolsDicExtractData;
use Apps\Core\Packages\Devtools\Test\DevtoolsTest;
use System\Base\BaseComponent;

class TestComponent extends BaseComponent
{
    protected $testPackage;

    public function initialize()
    {
        $this->testPackage = new DevtoolsTest;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        return;
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

    public function testAction()
    {
        if ($this->basepackages->progress->checkProgressFile()) {
            $this->basepackages->progress->deleteProgressFile();
        }

        $this->basepackages->progress->registerMethods(
            [
                [
                    'method'    => 'testTest',
                    'text'      => 'Test',
                ],
                [
                    'method'    => 'testTest',
                    'text'      => 'Test',
                ],
                [
                    'method'    => 'testTest',
                    'text'      => 'Test',
                ],
                [
                    'method'    => 'testDownload',
                    'text'      => 'Download Data...',
                    'remoteWeb' => true
                ],
                // [
                //     'method'    => 'testProcess',
                //     'text'      => 'Process Data...',
                //     'steps'     => true
                // ]
            ]
        );

        $this->testPackage->testTest();
        $this->testPackage->testTest();
        $this->testPackage->testTest();
        $this->testPackage->testDownload();

        $this->addResponse(
            $this->testPackage->packagesData->responseMessage,
            $this->testPackage->packagesData->responseCode
        );
    }
}