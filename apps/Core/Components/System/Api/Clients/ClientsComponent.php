<?php

namespace Apps\Core\Components\System\Api\Clients;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use System\Base\BaseComponent;
use System\Base\Exceptions\ControllerNotFoundException;

class ClientsComponent extends BaseComponent
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
        $this->view->apiCategories = $this->apiPackage->apiCategories;

        $this->view->apiLocations = $this->apiPackage->apiLocations;

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $api = $this->apiPackage->getApiById($this->getData()['id']);

                if (isset($this->getData()['repository'])) {
                    $api['repository'] = true;
                }
            } else {
                $api = [];
                $api['setup'] = 0;

                if (isset($this->getData()['repository'])) {
                    $api['category'] = 'repos';
                    $api['location'] = 'system';
                    $api['repository'] = true;
                }

                if (isset($this->getData()['category']) && isset($this->getData()['provider'])) {
                    $api['category'] = $this->getData()['category'];
                    $api['provider'] = $this->getData()['provider'];

                    //Check if provider class exists
                    if (!$this->apiPackage->useApi(['config' =>
                        ['category' => $this->getData()['category'],
                         'provider' => $this->getData()['provider'],
                         'test'     => true
                        ]
                    ])) {
                        throw new ControllerNotFoundException;
                    }

                    $api['location'] = $this->apiPackage->apiLocation;
                }
            }

            $this->view->api = $api;

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

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'system/api/clients',
                    'remove'    => 'system/api/clients/remove'
                ]
            ];

        $this->generateDTContent(
            $this->apiPackage,
            'system/api/clients/view',
            null,
            ['name', 'category', 'provider', 'in_use', 'used_by', 'setup'],
            true,
            ['name', 'category', 'provider', 'in_use', 'used_by', 'setup'],
            $controlActions,
            null,
            $replaceColumns,
            'name'
        );

        $this->view->pick('clients/list');
    }

    protected function replaceColumns($dataArr)
    {
        foreach ($dataArr as $dataKey => &$data) {
            if ($data['in_use'] == '0') {
                $data['in_use'] = '<span class="badge badge-secondary text-uppercase">No</span>';
            } else if ($data['in_use'] == '1') {
                $data['in_use'] = '<span class="badge badge-success text-uppercase">Yes</span>';
            }

            $data['category'] = ucfirst($data['category']);

            if ($data['used_by'] !== '') {
                $data['used_by'] = ucfirst($data['used_by']);
            } else if ($data['used_by'] === '') {
                $data['used_by'] = '-';
            }

            if ($data['setup'] == '1') {
                $data['setup'] ='<a href="' . $this->links->url('system/api/q/') . '" type="button" data-id="" data-rowid="" class="pl-2 pr-2 text-white btn btn-primary btn-xs rowSetup contentAjaxLink"><i class="mr-1 fas fa-fw fa-xs fa-magic"></i> <span class="text-xs text-uppercase">Complete Setup</span></a>';
            } else if ($data['setup'] == '2') {
                $data['setup'] = '<a href="' . $this->links->url('system/api/q/') . '" type="button" data-id="" data-rowid="" class="pl-2 pr-2 text-white btn btn-primary btn-xs rowSetup contentAjaxLink"><i class="mr-1 fas fa-fw fa-xs fa-magic"></i> <span class="text-xs text-uppercase">Complete Setup</span></a>';
            } else if ($data['setup'] == '3') {
                $data['setup'] = '<a href="' . $this->links->url('system/api/q/') . '" type="button" data-id="" data-rowid="" class="pl-2 pr-2 text-white btn btn-primary btn-xs rowSetup contentAjaxLink"><i class="mr-1 fas fa-fw fa-xs fa-magic"></i> <span class="text-xs text-uppercase">Complete Setup</span></a>';
            } else if ($data['setup'] == '4') {
                $data['setup'] = '<h6><span class="badge badge-success">Complete</span></h6>';
            } else if ($data['setup'] == '5') {
                $data['setup'] = '<a href="' . $this->links->url('system/api/q/') . '" type="button" data-id="" data-rowid="" class="pl-2 pr-2 text-white btn btn-primary btn-xs rowSetup contentAjaxLink"><i class="mr-1 fas fa-fw fa-xs fa-magic"></i> <span class="text-xs text-uppercase">Refresh Token</span></a>';//When token is about to expire <= 10 days
            }
        }

        return $dataArr;
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