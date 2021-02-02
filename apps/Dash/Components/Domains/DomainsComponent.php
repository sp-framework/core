<?php

namespace Apps\Dash\Components\Domains;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use System\Base\BaseComponent;

class DomainsComponent extends BaseComponent
{
    use DynamicTable;

    public function initialize()
    {
        //
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $domain = $this->domains->generateViewData($this->getData()['id']);
            } else {
                $domain = $this->domains->generateViewData();
            }
            if ($domain) {
                $this->view->domain = $this->domains->packagesData->domain;
            }
            $this->view->emailservices = $this->domains->packagesData->emailservices;

            $storages = $this->domains->packagesData->storages;
            $publicStorages = [];
            $privateStorages = [];

            foreach ($storages as $key => $storage) {
                if ($storage['permission'] === 'public') {
                    $publicStorages[$key] = $storage;
                } else if ($storage['permission'] === 'private') {
                    $privateStorages[$key] = $storage;
                }
            }

            $this->view->publicStorages = $publicStorages;

            $this->view->privateStorages = $privateStorages;

            $this->view->apps = $this->domains->packagesData->apps;

            $this->view->pick('domains/view');

            return;
        }

        $controlActions =
            [
                // 'disableActionsForIds'  => [1],
                'actionsToEnable'       =>
                [
                    'edit'      => 'domains',
                    'remove'    => 'domains/remove'
                ]
            ];

        $this->generateDTContent(
            $this->domains,
            'domains/view',
            null,
            ['name'],
            true,
            ['name'],
            $controlActions,
            null,
            null,
            'name'
        );

        $this->view->pick('domains/list');
    }

    /**
     * @acl(name="add")
     */
    public function addAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }
            $this->domains->addDomain($this->postData());

            $this->view->responseCode = $this->domains->packagesData->responseCode;

            $this->view->responseMessage = $this->domains->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    /**
     * @acl(name="update")
     */
    public function updateAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }
            $this->domains->updateDomain($this->postData());

            $this->view->responseCode = $this->domains->packagesData->responseCode;

            $this->view->responseMessage = $this->domains->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    /**
     * @acl(name="remove")
     */
    public function removeAction()
    {
        if ($this->request->isPost()) {

            $this->domains->removeDomain($this->postData());

            $this->view->responseCode = $this->domains->packagesData->responseCode;

            $this->view->responseMessage = $this->domains->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}