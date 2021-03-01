<?php

namespace Apps\Dash\Components\System\Api;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\System\Api\Api;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class ApiComponent extends BaseComponent
{
    use DynamicTable;

    protected $api;

    public function initialize()
    {
        $this->api = $this->usePackage(Api::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['action']) &&
            $this->getData()['action'] === 'addebaytoken'
        ) {
            $this->addEbayTokenAction();

            $this->view->setLayout('auth');

            $this->view->pick('api/types/ebay/wizard/addtoken');

            return;
        }

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {

                $api = $this->api->getApiById($this->getData()['id']);

                if ($api['api_type'] === 'ebay') {
                    try {
                        $ebayIds = include(base_path('apps/Dash/Packages/System/Api/Configs/EbayIds.php'));

                        $this->view->ebayIds = $ebayIds['ebay_ids'];
                    } catch (\Exception $e) {
                        throw new \Exception($e->getMessage());
                    }
                }
            } else {
                if ((isset($this->getData()['type']) && $this->getData()['type'] === 'ebay')) {
                    try {
                        $ebayIds = include(base_path('apps/Dash/Packages/System/Api/Configs/EbayIds.php'));

                        $this->view->ebayIds = $ebayIds['ebay_ids'];
                    } catch (\Exception $e) {
                        throw new \Exception($e->getMessage());
                    }
                }

                $api = [];

                $api['setup'] = 0;

                $api['api_type'] = $this->getData()['type'];
            }

            $this->view->api = $api;

            $this->view->responseCode = $this->api->packagesData->responseCode;

            $this->view->responseMessage = $this->api->packagesData->responseMessage;

            $this->view->pick('api/view');

            return;
        }

        $this->view->apiType = '';

        if ($this->request->isPost()) {
            $replaceColumns =
                [
                    'in_use'   => ['html'  =>
                        [
                            '0' => 'No',
                            '1' => 'Yes'
                        ]
                    ],
                    'setup'   => ['html'  =>
                        [
                            '1' => '<a href="' . $this->links->url('system/api/q/') . '" type="button" data-id="" data-rowid="" class="pl-2 pr-2 text-white btn btn-primary btn-xs rowSetup contentAjaxLink"><i class="mr-1 fas fa-fw fa-xs fa-magic"></i> <span class="text-xs text-uppercase">Complete Setup</span></a>',
                            '2' => '<a href="' . $this->links->url('system/api/q/') . '" type="button" data-id="" data-rowid="" class="pl-2 pr-2 text-white btn btn-primary btn-xs rowSetup contentAjaxLink"><i class="mr-1 fas fa-fw fa-xs fa-magic"></i> <span class="text-xs text-uppercase">Complete Setup</span></a>',
                            '3' => '<a href="' . $this->links->url('system/api/q/') . '" type="button" data-id="" data-rowid="" class="pl-2 pr-2 text-white btn btn-primary btn-xs rowSetup contentAjaxLink"><i class="mr-1 fas fa-fw fa-xs fa-magic"></i> <span class="text-xs text-uppercase">Complete Setup</span></a>',
                            '4' => '<h6><span class="badge badge-success">Complete</span></h6>',
                            '5' => '<a href="' . $this->links->url('system/api/q/') . '" type="button" data-id="" data-rowid="" class="pl-2 pr-2 text-white btn btn-primary btn-xs rowSetup contentAjaxLink"><i class="mr-1 fas fa-fw fa-xs fa-magic"></i> <span class="text-xs text-uppercase">Refresh Token</span></a>'//When token is about to expire <= 10 days
                        ]
                    ],
                ];
        } else {
            $replaceColumns = null;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'system/api',
                    'remove'    => 'system/api/remove'
                ]
            ];

        $this->generateDTContent(
            $this->api,
            'system/api/view',
            null,
            ['name', 'api_type', 'in_use', 'used_by', 'setup'],
            true,
            ['name', 'api_type', 'in_use', 'used_by', 'setup'],
            $controlActions,
            null,
            $replaceColumns,
            'name'
        );

        $this->view->pick('api/list');
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->api->addApi($this->postData())) {
                $this->view->responseData = $this->api->packagesData->last;
            }

            $this->view->responseCode = $this->api->packagesData->responseCode;

            $this->view->responseMessage = $this->api->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->api->updateApi($this->postData());

            $this->view->responseCode = $this->api->packagesData->responseCode;

            $this->view->responseMessage = $this->api->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->api->removeApi($this->postData());

            $this->view->responseCode = $this->api->packagesData->responseCode;

            $this->view->responseMessage = $this->api->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function getEbayAppTokenAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $api = $this->api->useApi($this->postData(['api_id']));

            $api->getAppToken();

            $this->view->responseCode = $api->packagesData->responseCode;

            $this->view->responseMessage = $api->packagesData->responseMessage;
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function getEbayUserTokenUrlAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $api = $this->api->useApi($this->postData(['api_id']));

            $api->getUserTokenUrl($this->random->uuid());

            $this->view->responseCode = $api->packagesData->responseCode;

            $this->view->responseMessage = $api->packagesData->responseMessage;

            $this->view->responseData = $api->packagesData->responseData;
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function checkEbayUserTokenAction()
    {
        if ($this->request->isPost()) {
            $api = $this->api->useApi($this->postData(['api_id']));

            $api->checkUserToken();

            $responseData = $api->packagesData->responseData;

            if (isset($this->postData()['get_user_data'])) {
                $responseData = array_merge($responseData, $this->refreshEbayUserdataAction($api));
            }

            $this->view->responseData = $responseData;

            $this->view->responseCode = $api->packagesData->responseCode;

            $this->view->responseMessage = $api->packagesData->responseMessage;
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    protected function addEbayTokenAction()
    {
        $api = $this->api->useApi($this->request->get());

        if ($api) {
            $api->addUserToken($this->request->get());

            $this->view->responseCode = $api->packagesData->responseCode;

            $this->view->responseMessage = $api->packagesData->responseMessage;
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'State received is incorrect.';
        }
    }

    public function refreshEbayUserdataAction($api = null)
    {
        if (!$api) {
            $api = $this->api->useApi($this->postData(['api_id']));

            $api->checkUserToken();

            $responseData = $api->packagesData->responseData;
        }

        $identity = $api->useService('Identityapi');

        $request = new \Apps\Dash\Packages\System\Api\Apis\Ebay\Identityapi\Operations\GetUserRestRequest;

        $response = $identity->getUser($request);

        $responseData['user_data'] = $response->toArray();

        $this->view->responseData = $responseData;

        $this->view->responseCode = $api->packagesData->responseCode;

        $this->view->responseMessage = $api->packagesData->responseMessage;

        return $responseData;
    }
}
