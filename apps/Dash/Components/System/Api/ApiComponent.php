<?php

namespace Apps\Dash\Components\System\Api;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\System\Api\Api;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;
use System\Base\Exceptions\ControllerNotFoundException;

class ApiComponent extends BaseComponent
{
    use DynamicTable;

    protected $apiPackage;

    public function initialize()
    {
        $this->apiPackage = $this->usePackage(Api::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        // if (isset($this->getData()['action']) &&
        //     $this->getData()['action'] === 'addebaytoken'
        // ) {
        //     $this->addEbayTokenAction();

        //     $this->view->setLayout('auth');

        //     $this->view->pick('api/types/ebay/wizard/addtoken');

        //     return;
        // }

        // if (isset($this->getData()['action']) &&
        //     $this->getData()['action'] === 'addxerotoken'
        // ) {
        //     $this->addXeroTokenAction();

        //     $this->view->setLayout('auth');

        //     $this->view->pick('api/types/xero/wizard/addtoken');

        //     return;
        // }

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $api = $this->apiPackage->getApiById($this->getData()['id']);

                if (!$api) {
                    return $this->throwIdNotFound();
                }

                if ($api['api_type'] === 'ebay') {
                    $this->includeEbayIds();
                }
            } else {
                // if ((isset($this->getData()['type']) && $this->getData()['type'] === 'ebay')) {
                //     $this->includeEbayIds();
                // }

                $api = [];

                $api['setup'] = 0;

                $api['api_type'] = $this->getData()['type'];

                $apiClass = $this->apiPackage->getApiClass($api['api_type']);

                try {
                    $api = (new $apiClass($api, $this->apiPackage))->init()->view();
                } catch (\Exception $e) {
                    throw new ControllerNotFoundException;
                }
            }

            $this->view->api = $api;

            $this->view->pick('api/view');

            return;
        }

        $this->view->apiTypes = $this->apiPackage->apiTypes;

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
            $this->apiPackage,
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

            if ($this->apiPackage->addApi($this->postData())) {
                $this->view->responseData = $this->apiPackage->packagesData->last;
            }

            $this->view->responseCode = $this->apiPackage->packagesData->responseCode;

            $this->view->responseMessage = $this->apiPackage->packagesData->responseMessage;

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

            $this->apiPackage->updateApi($this->postData());

            $this->view->responseCode = $this->apiPackage->packagesData->responseCode;

            $this->view->responseMessage = $this->apiPackage->packagesData->responseMessage;

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

            $this->apiPackage->removeApi($this->postData());

            $this->view->responseCode = $this->apiPackage->packagesData->responseCode;

            $this->view->responseMessage = $this->apiPackage->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    protected function includeEbayIds()
    {
        try {
            $ebayIds = include(base_path('apps/Dash/Packages/System/Api/Configs/Ebay/Ids.php'));

            $this->view->ebayIds = $ebayIds['ebay_ids'];
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getEbayAppTokenAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $api = $this->apiPackage->useApi($this->postData());

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

            $api = $this->apiPackage->useApi($this->postData());

            $api->getUserTokenUrl($this->random->uuid());

            $this->view->responseCode = $api->packagesData->responseCode;

            $this->view->responseMessage = $api->packagesData->responseMessage;

            $this->view->responseData = $api->packagesData->responseData;
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    protected function addEbayTokenAction()
    {
        $api = $this->apiPackage->useApi($this->request->get());

        if ($api) {
            $api->addUserToken($this->request->get());

            $this->view->responseCode = $api->packagesData->responseCode;

            $this->view->responseMessage = $api->packagesData->responseMessage;
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'State received is incorrect.';
        }
    }

    public function checkEbayUserTokenAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $api = $this->apiPackage->useApi($this->postData());

            $responseData = $api->packagesData->responseData;

            if (isset($this->postData()['get_user_data'])) {
                $responseData = array_merge($responseData, $api->getEbayUserdata());
            }

            $this->view->responseData = $responseData;

            $this->view->responseCode = $api->packagesData->responseCode;

            $this->view->responseMessage = $api->packagesData->responseMessage;
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    // protected function refreshEbayUserdataAction($api = null)
    // {
    //     if (!$api) {
    //         $api = $this->apiPackage->useApi($this->postData());

    //         $responseData = $api->packagesData->responseData;
    //     }

    //     $identity = $api->useService('EbayIdentityApi');

    //     $request = new \Apps\Dash\Packages\System\Api\Apis\Ebay\EbayIdentityApi\Operations\GetUserRestRequest;

    //     $response = $identity->getUser($request);

    //     $responseData['user_data'] = $response->toArray();

    //     if (isset($responseData['user_data']['accountType']) &&
    //         $responseData['user_data']['accountType'] === 'BUSINESS'
    //     ) {
    //         $trading = $api->useService('EbayTradingApi');

    //         $request = new \Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Operations\GetStoreRequest;

    //         $response = $trading->getStore($request);

    //         $responseArr = $response->toArray();

    //         if (isset($responseArr['Store'])) {
    //             $responseData['store_data']['name'] = $responseArr['Store']['Name'];
    //             $responseData['store_data']['url'] = $responseArr['Store']['URL'];
    //         }
    //     }

    //     $this->view->responseData = $responseData;

    //     $this->view->responseCode = $api->packagesData->responseCode;

    //     $this->view->responseMessage = $api->packagesData->responseMessage;

    //     return $responseData;
    // }

    public function checkEbayCallStatsAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $api = $this->apiPackage->useApi($this->postData());

            $callStats = $this->apiPackage->getApiCallStats($api->getApiConfig());

            if (count($callStats) === 0 ||
                isset($this->postData()['check_on_ebay'])
            ) {
                $callStats = array_merge($callStats, $api->refreshEbayCallStats());
            }

            $responseData = [];

            $responseData['timestamp'] = $callStats['timestamp'];

            foreach ($callStats['rateLimits'] as $key => $apiInfo) {
                if ($apiInfo['apiContext'] === 'sell' ||
                    $apiInfo['apiContext'] === 'commerce' ||
                    $apiInfo['apiContext'] === 'developer'
                ) {
                    if (isset($apiInfo['resources']) &&
                        is_array($apiInfo['resources']) &&
                        count($apiInfo['resources']) > 0
                    ) {
                        foreach ($apiInfo['resources'] as $resourceKey => $resource) {
                            if (isset($resource['rates']) &&
                                is_array($resource['rates']) &&
                                count($resource['rates']) > 0
                            ) {
                                foreach ($resource['rates'] as $ratesKey => $rates) {
                                    $responseData['callData'][ucfirst($apiInfo['apiContext'])][ucfirst($apiInfo['apiName'])][$ratesKey] = $rates;
                                    $date = new \DateTime($responseData['callData'][ucfirst($apiInfo['apiContext'])][ucfirst($apiInfo['apiName'])][$ratesKey]['reset']);
                                    $responseData['callData'][ucfirst($apiInfo['apiContext'])][ucfirst($apiInfo['apiName'])][$ratesKey]['reset'] =
                                        $date->format('Y-m-d H:i:s');
                                }
                            }
                        }
                    }
                }
            }

            $this->view->responseData = $responseData;

            $this->view->responseCode = 0;

            $this->view->responseMessage = 'Retrieved Call Stats Information.';
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function getXeroUserTokenUrlAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $api = $this->apiPackage->useApi($this->postData());

            $api->getUserTokenUrl($this->random->uuid());

            $this->view->responseData = $api->packagesData->responseData;

            $this->view->responseCode = $api->packagesData->responseCode;

            $this->view->responseMessage = $api->packagesData->responseMessage;
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function getXeroTenantsAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $api = $this->apiPackage->useApi($this->postData());

            $this->view->responseData = $api->getTenants();

            $this->view->responseCode = 0;

            $this->view->responseMessage = '';
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    protected function addXeroTokenAction()
    {
        $api = $this->apiPackage->useApi($this->request->get());

        if ($api) {
            $api->addUserToken($this->request->get());

            $this->view->responseCode = $api->packagesData->responseCode;

            $this->view->responseMessage = $api->packagesData->responseMessage;
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'State received is incorrect.';
        }
    }

    public function checkXeroAuthoAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $api = $this->apiPackage->useApi($this->postData());

            $responseData = $api->packagesData->responseData;

            $this->view->responseData = $responseData;

            $this->view->responseCode = $api->packagesData->responseCode;

            $this->view->responseMessage = $api->packagesData->responseMessage;
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function checkXeroCallStatsAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $api = $this->apiPackage->useApi($this->postData());

            $callStats = $api->refreshXeroCallStats();

            $responseData = [];

            if (count($callStats) === 0) {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'Could not retrieve call stats information';

                $this->view->responseData = $responseData;

                return;
            }

            $responseData['timestamp'] = $callStats['timestamp'];

            $responseData['callData'] = $callStats['rateLimits'];

            $this->view->responseData = $responseData;

            $this->view->responseCode = 0;

            $this->view->responseMessage = 'Retrieved Call Stats Information.';
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}