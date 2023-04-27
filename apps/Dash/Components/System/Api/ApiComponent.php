<?php

namespace Apps\Dash\Components\System\Api;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use System\Base\BaseComponent;
use System\Base\Exceptions\ControllerNotFoundException;

class ApiComponent extends BaseComponent
{
    use DynamicTable;

    protected $apiPackage;

    public function initialize()
    {
        $this->apiPackage = $this->basepackages->api;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $api = $this->apiPackage->getApiById($this->getData()['id']);
            } else {
                $api = [];
                $api['setup'] = 0;
                $api['category'] = $this->getData()['category'];
                $api['type'] = $this->getData()['type'];

                try {
                    $apiClass = $this->apiPackage->getApiClass(
                        $this->getData()['category'] . '/' . $this->getData()['type'] . '/' . $this->getData()['type'], false
                    );

                    (new $apiClass($api, $this->apiPackage))->init();
                } catch (\throwable $e) {
                    try {
                        $apiClass = $this->apiPackage->getApiClass(
                            $this->getData()['category'] . '/' . $this->getData()['type'] . '/' . $this->getData()['type']
                        );

                        (new $apiClass($api, $this->apiPackage))->init();
                    } catch (\throwable $e) {
                        throw new ControllerNotFoundException;
                    }
                }
            }

            $this->view->api = $api;

            $this->view->pick('api/view');

            return;
        }

        $this->view->apiCategories = $this->apiPackage->apiCategories;

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
            ['name', 'category', 'provider_name', 'in_use', 'used_by', 'setup'],
            true,
            ['name', 'category', 'provider_name', 'in_use', 'used_by', 'setup'],
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
}