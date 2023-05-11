<?php

namespace Apps\Core\Components\Domains;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
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

                if (!$domain) {
                    return $this->throwIdNotFound();
                }
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

        $replaceColumns =
            function ($dataArr) {
                if ($dataArr && is_array($dataArr) && count($dataArr) > 0) {
                    return $this->replaceColumns($dataArr);
                }

                return $dataArr;
            };

        $this->generateDTContent(
            $this->domains,
            'domains/view',
            null,
            ['name', 'default_app_id', 'exclusive_to_default_app', 'is_internal'],
            true,
            ['name', 'default_app_id', 'exclusive_to_default_app', 'is_internal'],
            $controlActions,
            ['default_app_id' => 'default app'],
            $replaceColumns,
            'name'
        );

        $this->view->pick('domains/list');
    }

    protected function replaceColumns($dataArr)
    {
        foreach ($dataArr as $dataKey => &$data) {
            $data = $this->formatDefaultAppId($dataKey, $data);
            $data = $this->formatExclusiveToDefaultApp($dataKey, $data);
            $data = $this->formatIsInternal($dataKey, $data);
        }

        return $dataArr;
    }

    protected function formatDefaultAppId($rowId, $data)
    {
        if ($data['default_app_id'] == '0') {
            $data['default_app_id'] = '<span class="badge badge-danger text-uppercase">ERROR: NO APP</span>';
        } else {
            $app = $this->apps->getIdApp($data['default_app_id']);

            if ($app) {
                $data['default_app_id'] = '<span class="badge badge-primary text-uppercase">' . $app['name'] . '</span>';
            } else {
                $data['default_app_id'] = '<span class="badge badge-danger text-uppercase">ERROR: NO APP</span>';
            }
        }

        return $data;
    }

    protected function formatExclusiveToDefaultApp($rowId, $data)
    {
        if ($data['exclusive_to_default_app'] == '0') {
            $data['exclusive_to_default_app'] = '<span class="badge badge-secondary text-uppercase">No</span>';
        } else if ($data['exclusive_to_default_app'] == '1') {
            $data['exclusive_to_default_app'] = '<span class="badge badge-primary text-uppercase">Yes</span>';
        }

        return $data;
    }

    protected function formatIsInternal($rowId, $data)
    {
        if ($data['is_internal'] == '0') {
            $data['is_internal'] = '<span class="badge badge-primary text-uppercase">No</span>';
        } else if ($data['is_internal'] == '1') {
            $data['is_internal'] = '<span class="badge badge-secondary text-uppercase">Yes</span>';
        }

        return $data;
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

    public function validateDomainAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->postData()['name']) {
                $domainDetails = $this->domains->validateDomain($this->postData()['name']);

                if ($domainDetails) {
                    $this->view->domainDetails = $this->domains->packagesData->domainDetails;

                    if ($this->view->domainDetails['internal'] === true) {
                        $this->view->responseMessage = 'Domain details not found on the internet.';
                        $this->view->responseCode = 3;
                    } else if ($this->view->domainDetails['internal'] === false && $this->view->domainDetails['matched'] === false) {
                        $this->view->responseMessage = 'Domain details found on the internet, but did not match.';
                        $this->view->responseCode = 4;
                    } else {
                        $this->view->responseCode = 0;
                        $this->view->responseMessage = 'Domain details found on the internet.';
                    }

                    return;
                }

                $this->view->responseCode = 1;

                $this->view->responseMessage = 'Domain details not found!';
            }
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}