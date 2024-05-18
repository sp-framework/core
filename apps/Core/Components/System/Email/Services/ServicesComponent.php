<?php

namespace Apps\Core\Components\System\Email\Services;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use System\Base\BaseComponent;

class ServicesComponent extends BaseComponent
{
    use DynamicTable;

    protected $emailservices;

    public function initialize()
    {
        $this->emailservices = $this->basepackages->emailservices;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $emailservice = $this->emailservices->getById($this->getData()['id']);

                if (!$emailservice) {
                    return $this->throwIdNotFound();
                }

                $this->view->emailservice = $emailservice;
            }
            $this->view->pick('services/view');

            return;
        }

        $emailservices = $this->emailservices->init();

        $controlActions =
            [
                // 'disableActionsForIds'  => [1],
                'actionsToEnable'       =>
                [
                    'edit'      => 'system/email/services',
                    'remove'    => 'system/email/services/remove'
                ]
            ];

        if ($this->request->isPost()) {
            $replaceColumns =
                [
                    'encryption' =>
                        [
                            'html'  =>
                            [
                                '0' => '<span class="badge badge-secondary text-uppercase">Disabled</span>',
                                '1' => '<span class="badge badge-success text-uppercase">Enabled</span>'
                            ]
                        ]
                ];
        } else {
            $replaceColumns = null;
        }

        $this->generateDTContent(
            $emailservices,
            'system/email/services/view',
            null,
            ['name', 'host', 'port', 'encryption'],
            false,
            ['name', 'host', 'port', 'encryption'],
            $controlActions,
            null,
            $replaceColumns,
            'name'
        );

        $this->view->pick('services/list');
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