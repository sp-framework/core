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
        $passwordApis = $this->api->getEnabledAPIByType('password');

        if ($passwordApis && is_array($passwordApis) && count($passwordApis) > 0) {
            foreach ($passwordApis as $passwordApiKey => $passwordApi) {
                if ($passwordApi['client_keys_generation_allowed'] == true) {
                    unset($passwordApis[$passwordApiKey]);
                }
            }
        } else {
            $passwordApis = [];
        }

        $clientCredentialsApis = $this->api->getEnabledAPIByType('client_credentials');

        if ($clientCredentialsApis && is_array($clientCredentialsApis) && count($clientCredentialsApis) > 0) {
            foreach ($clientCredentialsApis as $clientCredentialsApiKey => $clientCredentialsApi) {
                if ($clientCredentialsApi['client_keys_generation_allowed'] == true) {
                    unset($clientCredentialsApis[$clientCredentialsApiKey]);
                }
            }
        } else {
            $clientCredentialsApis = [];
        }

        $this->view->availableAPIServices = array_merge($passwordApis, $clientCredentialsApis);

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

       $conditions =
            [
                'order'         => 'id desc'
            ];

        $this->generateDTContent(
            $this->api->clients,
            'system/api/server/clients/view',
            $conditions,
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
                    '<a id="' . strtolower($this->app['route']) . '-' . strtolower($this->componentName) . '-revoke-' . $dataKey . '" href="' . $this->links->url('system/api/server/clients/forceRevoke') . '" type="button" data-id="' . $data['id'] . '" data-rowid="' . $dataKey . '" class="ml-1 mr-1 text-white btn btn-danger btn-xs rowRevoke text-uppercase" data-toggle="tooltip" data-placement="auto" title="Revoke">
                        <i class="fas fa-fw fa-xs fa-times-circle"></i>
                    </a>' .
                    '<a id="' . strtolower($this->app['route']) . '-' . strtolower($this->componentName) . '-revokeregen-' . $dataKey . '" href="' . $this->links->url('system/api/server/clients/forceRevoke') . '" type="button" data-id="' . $data['id'] . '" data-rowid="' . $dataKey . '" class="ml-1 mr-1 text-white btn btn-warning btn-xs rowRevokeRegen text-uppercase" data-toggle="tooltip" data-placement="auto" title="Revoke & Regenerate">
                        <i class="fas fa-fw fa-xs fa-sync-alt"></i>
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
        $this->requestIsPost();

        $this->api->clients->addClient($this->postData());

        $this->addResponse(
            $this->api->clients->packagesData->responseMessage,
            $this->api->clients->packagesData->responseCode
        );
    }

    /**
     *
     */
    public function updateAction()
    {
        $this->requestIsPost();
        //
    }

    public function removeAction()
    {
        return false;
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