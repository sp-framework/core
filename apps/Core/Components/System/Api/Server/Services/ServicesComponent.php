<?php

namespace Apps\Core\Components\System\Api\Server\Services;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToReadFile;
use System\Base\BaseComponent;

class ServicesComponent extends BaseComponent
{
    use DynamicTable;

    protected $apiClients;

    public function initialize()
    {
        $this->apiClients = $this->api->init()->clients;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $this->view->availableAPIScopes = $this->api->getAPIAvailableScopes();
        $this->view->availableAPITypes = $this->api->getAPIAvailableTypes();
        $this->view->availableAPIGrantTypes = $this->api->getAvailableAPIGrantTypes();

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $api = $this->api->getById($this->getData()['id']);
            } else {
                $api = [];
            }

            $api['redirect_url'] = '';
            $api['request_url'] = '';

            if (isset($api['client_id']) && $api['client_id'] !== '') {
                $client = $this->apiClients->getFirst('client_id', $api['client_id']);

                if ($client) {
                    $client = $client->toArray();

                    if ($client['redirectUri'] === '' || $client['redirectUri'] === 'https://') {
                        $api['type'] = 'redirect';
                        $api['redirect_url'] = $this->api->generateAPIUrl($api);
                    } else {
                        $api['redirect_url'] = $client['redirectUri'];
                    }
                    if (isset($client['client_secret']) && $client['client_secret'] !== '') {
                        $client['client_secret'] = $this->random->base58(8);
                    }
                    $api['client_secret'] = $client['client_secret'];
                } else {
                    $client['redirectUri'] = 'https://';
                    $api['type'] = 'redirect';
                    $api['redirect_url'] = $this->api->generateAPIUrl($api);
                    $api['client_secret'] = '';
                }

                $api['type'] = 'request';
                $api['request_url'] = $this->api->generateAPIUrl($api);
            } else {
                $api['client_secret'] = '';
            }

            if ($this->getData()['id'] != 0) {//Read dev provided openapi information via Install.php file
                try {
                    $version = '0.0.0';

                    if ($this->apps->apps[$api['app_id']]['app_type'] === 'core') {
                        $version = $this->core->core['version'];
                    } else {
                        try {
                            if ($this->localContent->fileExists('apps/' . ucfirst($this->apps->apps[$api['app_id']]['app_type']) . '/Install/type.json')) {
                                $type = $this->helper->decode($this->localContent->read('apps/' . ucfirst($this->apps->apps[$api['app_id']]['app_type']) . '/Install/type.json'), true);

                                if (isset($type['version'])) {
                                    $version = $type['version'];
                                }
                            }
                        } catch (\throwable | UnableToCheckExistence | UnableToReadFile | FilesystemException $e) {
                            $this->logException($e);

                            $this->addResponse($e->getMessage(), 1);

                            return false;
                        }
                    }

                    $result =
                        (new \OpenApi\Builder())
                        ->setSources(
                            [
                                base_path('apps/' . ucfirst($this->apps->apps[$api['app_id']]['app_type']) . '/Install/Install.php')
                            ]
                        )
                        ->setVersion($version)
                        ->build();

                    if ($result) {
                        $result = $this->helper->decode($result->toJson(), true);

                        if (isset($result['info']['title']) && $api['openapi_name'] === '') {
                            $api['openapi_name'] = $result['info']['title'];
                        }
                        if (isset($result['info']['description']) && $api['openapi_description'] === '') {
                            $api['openapi_description'] = $result['info']['description'];
                        }
                        if (isset($result['info']['contact']['email']) && $api['openapi_email'] === '') {
                            $api['openapi_email'] = $result['info']['contact']['email'];
                        }
                        if (isset($result['info']['license']['name']) && $api['openapi_license_name'] === '') {
                            $api['openapi_license_name'] = $result['info']['license']['name'];
                        }
                        if (isset($result['info']['license']['url']) && $api['openapi_license_url'] === '') {
                            $api['openapi_license_url'] = $result['info']['license']['url'];
                        }

                        if (isset($result['servers']) && count($result['servers']) > 0) {
                            foreach ($result['servers'] as $server) {
                                if (str_contains($server['url'], 'sandbox')) {
                                    $api['openapi_server_sandbox_url'] = $server['url'];
                                    $api['openapi_server_sandbox_description'] = $server['description'];
                                } else {
                                    $api['openapi_server_production_url'] = $server['url'];
                                    $api['openapi_server_production_description'] = $server['description'];
                                }
                            }
                        }
                    }
                } catch (\throwable $e) {
                    //Do nothing!
                }
            }

            $this->view->api = $api;
            $this->view->apps = $this->apps->apps;
            $this->view->domains = $this->domains->domains;
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

        if ($this->access->auth->account()['security']['role_id'] != '1') {
            $conditions =
                [
                    'conditions'    =>
                        '-|account_id|equals|' . $this->access->auth->account()['id'] . '&',
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
            ['name', 'status', 'api_type', 'registration_allowed', 'app_id', 'domain_id', 'grant_type', 'scope_id', 'account_id'],
            true,
            ['name', 'status', 'api_type', 'registration_allowed', 'app_id', 'domain_id', 'grant_type', 'scope_id', 'account_id'],
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
            if ($data['api_type'] === 'public' || $data['api_type'] === 'protected_user_credentials') {
                $data['grant_type'] = '-';
            }

            $data['api_type'] = $this->view->availableAPITypes[$data['api_type']]['name'];

            if ($data['grant_type'] !== '-') {
                $data['grant_type'] = $this->view->availableAPIGrantTypes[$data['grant_type']]['name'];
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

    public function generateOpenapiFileAction()
    {
        $this->requestIsPost();

        $this->api->generateOpenapiFile($this->postData());

        $this->addResponse(
            $this->api->packagesData->responseMessage,
            $this->api->packagesData->responseCode,
            $this->api->packagesData->responseData ?? []
        );
    }
}