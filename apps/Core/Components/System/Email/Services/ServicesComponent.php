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
        $this->requestIsPost();

        $this->emailservices->addEmailService($this->postData());

        $this->addResponse(
            $this->emailservices->packagesData->responseMessage,
            $this->emailservices->packagesData->responseCode
        );
    }

    /**
     * @acl(name="update")
     */
    public function updateAction()
    {
        $this->requestIsPost();

        $this->emailservices->updateEmailService($this->postData());

        $this->addResponse(
            $this->emailservices->packagesData->responseMessage,
            $this->emailservices->packagesData->responseCode
        );
    }

    /**
     * @acl(name="remove")
     */
    public function removeAction()
    {
        $this->requestIsPost();

        $this->emailservices->removeEmailService($this->postData());

        $this->addResponse(
            $this->emailservices->packagesData->responseMessage,
            $this->emailservices->packagesData->responseCode
        );
    }

    public function testEmailServiceAction()
    {
        $this->requestIsPost();

        $this->emailservices->testEmailService($this->postData());

        $this->addResponse(
            $this->emailservices->packagesData->responseMessage,
            $this->emailservices->packagesData->responseCode
        );
    }
}