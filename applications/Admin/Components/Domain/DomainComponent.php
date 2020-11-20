<?php

namespace Applications\Admin\Components\Domain;

use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class DomainComponent extends BaseComponent
{
    /**
     * @acl(name="view")
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            $domain = $this->modules->domains->getById($this->getData()['id']);

            $this->view->domain = $domain;
        }

        $this->view->applications = $this->modules->applications->applications;
    }

    /**
     * @acl(name="add")
     */
    public function addAction()
    {
        if ($this->request->isPost()) {

            $this->modules->domains->addDomain($this->postData());

            $this->view->responseCode = $this->modules->domains->packagesData->responseCode;

            $this->view->responseMessage = $this->modules->domains->packagesData->responseMessage;

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

            $this->modules->domains->updateDomain($this->postData());

            $this->view->responseCode = $this->modules->domains->packagesData->responseCode;

            $this->view->responseMessage = $this->modules->domains->packagesData->responseMessage;

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

            $this->modules->domains->removeDomain($this->postData());

            $this->view->responseCode = $this->modules->domains->packagesData->responseCode;

            $this->view->responseMessage = $this->modules->domains->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}