<?php

namespace Applications\Ecom\Admin\Components\Domains;

use Applications\Ecom\Admin\Packages\AdminLTETags\Traits\DynamicTable;
use System\Base\BaseComponent;

class DomainsComponent extends BaseComponent
{
    use DynamicTable;
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $domain = $this->basepackages->domains->generateViewData($this->getData()['id']);
            } else {
                $domain = $this->basepackages->domains->generateViewData();
            }
            if ($domain) {
                $this->view->domain = $this->basepackages->domains->packagesData->domain;
            }
            $this->view->emailservices = $this->basepackages->domains->packagesData->emailservices;

            $this->view->applications = $this->basepackages->domains->packagesData->applications;

            $this->view->pick('domains/view');

            return;
        }

        $domains = $this->basepackages->domains->init();

        $controlActions =
            [
                // 'disableActionsForIds'  => [1],
                'actionsToEnable'       =>
                [
                    'edit'      => 'domains',
                    'remove'    => 'domains/remove'
                ]
            ];

        $this->generateDTContent(
            $domains,
            'domains/view',
            null,
            ['name'],
            false,
            ['name'],
            $controlActions,
            null,
            null,
            'name'
        );

        $this->view->pick('domains/list');
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
            $this->basepackages->domains->addDomain($this->postData());

            $this->view->responseCode = $this->basepackages->domains->packagesData->responseCode;

            $this->view->responseMessage = $this->basepackages->domains->packagesData->responseMessage;

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
            $this->basepackages->domains->updateDomain($this->postData());

            $this->view->responseCode = $this->basepackages->domains->packagesData->responseCode;

            $this->view->responseMessage = $this->basepackages->domains->packagesData->responseMessage;

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

            $this->basepackages->domains->removeDomain($this->postData());

            $this->view->responseCode = $this->basepackages->domains->packagesData->responseCode;

            $this->view->responseMessage = $this->basepackages->domains->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}