<?php

namespace Applications\Admin\Components\Emailservice;

use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class EmailserviceComponent extends BaseComponent
{
    /**
     * @acl(name="view")
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            $emailservice = $this->emailservices->getById($this->getData()['id']);

            $this->view->emailservice = $emailservice;
        }
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

            $this->emailservices->addEmailService($this->postData());

            $this->view->responseCode = $this->emailservices->packagesData->responseCode;

            $this->view->responseMessage = $this->emailservices->packagesData->responseMessage;

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

            $this->emailservices->updateEmailService($this->postData());

            $this->view->responseCode = $this->emailservices->packagesData->responseCode;

            $this->view->responseMessage = $this->emailservices->packagesData->responseMessage;

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

            $this->emailservices->removeEmailService($this->postData());

            $this->view->responseCode = $this->emailservices->packagesData->responseCode;

            $this->view->responseMessage = $this->emailservices->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function testEmailServiceAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }
            $this->emailservices->testEmailService($this->postData());

            $this->view->responseCode = $this->emailservices->packagesData->responseCode;

            $this->view->responseMessage = $this->emailservices->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}