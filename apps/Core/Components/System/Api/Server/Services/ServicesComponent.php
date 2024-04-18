<?php

namespace Apps\Core\Components\System\Api\Server\Services;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use System\Base\BaseComponent;

class ServicesComponent extends BaseComponent
{
    use DynamicTable;

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $this->view->availableAPIScopes = $this->api->getAPIAvailableScopes();

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $api = $this->api->getById($this->getData()['id']);
            } else {
                $api = [];
            }

            $api['redirect_url'] = '';
            $api['request_url'] = '';

            if (isset($api['client_id']) && $api['client_id'] !== '') {
                $client = $this->api->clients->getFirst('client_id', $api['client_id']);

                if ($client) {
                    $client = $client->toArray();
                }

                if ($client['redirectUri'] === '' || $client['redirectUri'] === 'https://') {
                    $api['type'] = 'redirect';
                    $api['redirect_url'] = $this->api->generateAPIUrl($api);
                } else {
                    $api['redirect_url'] = $client['redirectUri'];
                }

                $api['type'] = 'request';
                $api['request_url'] = $this->api->generateAPIUrl($api);
            }

            $this->view->api = $api;
            $this->view->apps = $this->apps->apps;
            $this->view->domains = $this->domains->domains;
            $this->view->availableAPIGrantTypes = $this->api->getAvailableAPIGrantTypes();
            $this->view->availableOpensslKeyBits = $this->api->getOpensslKeyBits();
            $this->view->availableOpensslAlgorithms = $this->api->getOpensslAlgorithms();
            $this->view->apiKeysParams = $this->api->getAPIKeysParams($this->getData()['id']);

            $this->view->pick('services/view');

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

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'system/api/server/services',
                    'remove'    => 'system/api/server/services/remove'
                ]
            ];

        if ($this->auth->account()['security']['role_id'] != '1') {
            $conditions =
                [
                    'conditions'    =>
                        '-|account_id|equals|' . $this->auth->account()['id'] . '&',
                    'order'         => 'id desc'
                ];
        } else {
            $conditions =
                [
                    'order'         => 'id desc'
                ];
        }

        $this->generateDTContent(
            $this->api,
            'system/api/server/services/view',
            $conditions,
            ['name', 'status', 'is_public', 'registration_allowed', 'app_id', 'domain_id', 'grant_type', 'scope_id', 'account_id'],
            true,
            ['name', 'status', 'is_public', 'registration_allowed', 'app_id', 'domain_id', 'grant_type', 'scope_id', 'account_id'],
            $controlActions,
            ['app_id' => 'app', 'domain_id' => 'domain', 'scope_id' => 'scope', 'account_id' => 'Account'],
            $replaceColumns,
            'name'
        );

        $this->view->pick('services/list');
    }

    protected function replaceColumns($dataArr)
    {
        foreach ($dataArr as $dataKey => &$data) {
            if ($data['registration_allowed'] == '0') {
                $data['registration_allowed'] = '<span class="badge badge-secondary text-uppercase">No</span>';
            } else if ($data['registration_allowed'] == '1') {
                $data['registration_allowed'] = '<span class="badge badge-success text-uppercase">Yes</span>';
            }
            if ($data['status'] == '0') {
                $data['status'] = '<span class="badge badge-secondary text-uppercase">No</span>';
            } else if ($data['status'] == '1') {
                $data['status'] = '<span class="badge badge-success text-uppercase">Yes</span>';
            }
            if ($data['is_public'] == '0') {
                $data['is_public'] = '<span class="badge badge-secondary text-uppercase">No</span>';
            } else if ($data['is_public'] == '1') {
                $data['is_public'] = '<span class="badge badge-warning text-uppercase">Yes</span>';
                $data['grant_type'] = '-';
            }
            $app = $this->apps->getById($data['app_id']);
            if ($app) {
                $data['app_id'] = $app['name'];
            }
            $domain = $this->domains->getById($data['domain_id']);
            if ($domain) {
                $data['domain_id'] = $domain['name'];
            }
            $scope = $this->api->scopes->getById($data['scope_id']);
            if ($scope) {
                $data['scope_id'] = $scope['scope_name'];
            }

            if ($data['account_id'] && $data['account_id'] != 0) {
                $account = $this->basepackages->accounts->getById($data['account_id']);

                if ($account) {
                    $data['account_id'] = $account['email'];
                }
            } else {
                $data['account_id'] = '-';
            }
        }

        return $dataArr;
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        $this->requestIsPost();

        if ($this->api->addApi($this->postData())) {
            $this->view->responseData = $this->api->packagesData->last;
        }

        $this->view->responseCode = $this->api->packagesData->responseCode;

        $this->view->responseMessage = $this->api->packagesData->responseMessage;
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        $this->requestIsPost();

        $this->api->updateApi($this->postData());

        $this->addResponse(
            $this->api->packagesData->responseMessage,
            $this->api->packagesData->responseCode
        );
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        $this->requestIsPost();

        $this->api->removeApi($this->postData());

        $this->addResponse(
            $this->api->packagesData->responseMessage,
            $this->api->packagesData->responseCode
        );
    }

    public function generateAPIUrlAction()
    {
        $this->requestIsPost();

        $this->api->generateAPIUrl($this->postData());

        $this->addResponse(
            $this->api->packagesData->responseMessage,
            $this->api->packagesData->responseCode
        );

        if ($this->api->packagesData->responseData) {
            $this->view->responseData = $this->api->packagesData->responseData;
        }
    }
}