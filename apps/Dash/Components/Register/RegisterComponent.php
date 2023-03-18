<?php

namespace Apps\Dash\Components\Register;

use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class RegisterComponent extends BaseComponent
{
    protected $accounts;

    public function initialize()
    {
        $this->accounts = $this->basepackages->accounts;
    }

    public function viewAction()
    {
        $this->view->setLayout('auth');

        $domain = $this->domains->getDomain();

        if ($this->request->isAjax()) {
            $this->view->disable();
        }
    }

    public function addAction()
    {
        return;
    }

    public function updateAction()
    {
        return;
    }

    public function removeAction()
    {
        return;
    }

    public function registerNewAccountAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->accounts->registerAccount($this->postData());

            $this->view->redirectUrl = $this->accounts->packagesData->redirectUrl;

            $this->addResponse($this->accounts->packagesData->responseMessage, $this->accounts->packagesData->responseCode);
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}