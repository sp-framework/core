<?php

namespace Apps\Core\Components\System\Api\Server\Clients;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use System\Base\BaseComponent;

class ClientsComponent extends BaseComponent
{
    use DynamicTable;

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $client = $this->api->clients->generateViewData($this->getData()['id']);

                if (!$client) {
                    return $this->throwIdNotFound();
                }
            } else {
                $client = $this->api->clients->generateViewData();
            }

            if ($client) {
                $this->view->components = $this->api->clients->packagesData->components;

                $this->view->acls = $this->api->clients->packagesData->acls;

                $this->view->client = $this->api->clients->packagesData->client;

                $this->view->apps = $this->api->clients->packagesData->apps;

                $this->view->clients = $this->api->clients->packagesData->clients;
            }

            $this->addResponse(
                $this->api->clients->packagesData->responseMessage,
                $this->api->clients->packagesData->responseCode
            );

            $this->view->pick('clients/view');

            return;
        }

        $controlActions =
            [
                // 'disableActionsForIds'  => [1],
                'actionsToEnable'       =>
                [
                    'edit'      => 'system/api/server/clients',
                    'remove'    => 'system/api/server/clients/remove'
                ]
            ];

        $this->generateDTContent(
            $this->api->clients,
            'system/api/server/clients/view',
            null,
            ['name'],
            true,
            ['name'],
            $controlActions,
            [],
            null,
            'name'
        );

        $this->view->pick('clients/list');
    }

    /**
     * @acl(name="add")
     */
    public function addAction()
    {
        $this->requestIsPost();

        $this->api->clients->addClient($this->postData());

        $this->addResponse(
            $this->api->clients->packagesData->responseMessage,
            $this->api->clients->packagesData->responseCode
        );
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

            $this->api->clients->updateClient($this->postData());

            $this->addResponse(
                $this->api->clients->packagesData->responseMessage,
                $this->api->clients->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    /**
     * @acl(name="remove")
     */
    public function removeAction()
    {
        if ($this->request->isPost()) {

            $this->api->clients->removeClient($this->postData());

            $this->addResponse(
                $this->api->clients->packagesData->responseMessage,
                $this->api->clients->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}