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
                $client = $this->api->clients->getById($this->getData()['id']);

                if (!$client) {
                    return $this->throwIdNotFound();
                }

                $this->view->client = $client;
            }

            $this->view->pick('clients/view');

            return;
        }

        if ($this->request->isPost()) {
            $replaceColumns =
                function ($dataArr) {
                    if ($dataArr && is_array($dataArr) && count($dataArr) > 0) {
                        return $this->replaceColumns($dataArr);
                    }

                    return $dataArr;
                };
        } else {
            $replaceColumns = null;
        }

        $this->generateDTContent(
            $this->api->clients,
            'system/api/server/clients/view',
            null,
            ['revoked', 'client_id', 'api_id', 'app_id', 'domain_id', 'account_id', 'last_used'],
            true,
            ['revoked', 'client_id', 'api_id', 'app_id', 'domain_id', 'account_id', 'last_used'],
            null,
            ['api_id' => 'api', 'app_id' => 'app', 'domain_id' => 'domain', 'account_id' => 'account'],
            $replaceColumns,
            'client_id'
        );

        $this->view->pick('clients/list');
    }


    protected function replaceColumns($dataArr)
    {
        foreach ($dataArr as $dataKey => &$data) {
            if ($data['revoked'] == '1') {
                $data['revoked'] = '<span class="badge badge-primary text-uppercase">Revoked</span>';
            } else if ($data['revoked'] == '0') {
                $data['revoked'] =
                    '<a id="' . strtolower($this->app['route']) . '-' . strtolower($this->componentName) . '-send-' . $dataKey . '" href="' . $this->links->url('system/api/server/clients/forceRevoke') . '" type="button" data-id="' . $data['id'] . '" data-rowid="' . $dataKey . '" class="ml-1 mr-1 text-white btn btn-warning btn-xs rowRevoke text-uppercase">
                        <i class="mr-1 fas fa-fw fa-xs fa-times-circle"></i>
                        <span class="text-xs"> Revoke</span>
                    </a>';
            }

            $api = $this->api->getById($data['api_id']);
            if ($api) {
                $data['api_id'] = $api['name'];
            }

            $app = $this->apps->getById($data['app_id']);
            if ($app) {
                $data['app_id'] = $app['name'];
            }

            $domain = $this->domains->getById($data['domain_id']);
            if ($domain) {
                $data['domain_id'] = $domain['name'];
            }

            $account = $this->basepackages->accounts->getById($data['account_id']);
            if ($account) {
                $data['account_id'] = $account['email'];
            }
        }

        return $dataArr;
    }

    /**
     *
     */
    public function addAction()
    {
        //
    }

    /**
     *
     */
    public function updateAction()
    {
        //
    }

    public function removeAction()
    {
        //
    }

    public function forceRevokeAction()
    {
        $this->requestIsPost();

        $this->api->clients->forceRevoke($this->postData());

        $this->addResponse(
            $this->api->clients->packagesData->responseMessage,
            $this->api->clients->packagesData->responseCode
        );
    }

    public function generateClientKeysAction()
    {
        if (isset($this->checkPermissions()['update']) && $this->checkPermissions()['update'] == 0) {
            $this->addResponse('Permission Denied', 1);

            return;
        }

        $this->requestIsPost();

        $this->api->clients->generateClientKeys($this->postData());

        $this->addResponse(
            $this->api->clients->packagesData->responseMessage,
            $this->api->clients->packagesData->responseCode,
            $this->api->clients->packagesData->responseData
        );
    }

    public function generateClientIdAction()
    {
        $this->requestIsPost();

        $this->api->clients->generateClientId($this->postData());

        $this->addResponse(
            $this->api->clients->packagesData->responseMessage,
            $this->api->clients->packagesData->responseCode,
            $this->api->clients->packagesData->responseData
        );
    }
}