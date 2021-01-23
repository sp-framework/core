<?php

namespace Applications\Dash\Components\Hrms\Statuses;

use Applications\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Applications\Dash\Packages\Hrms\Statuses\HrmsStatuses;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class StatusesComponent extends BaseComponent
{
    use DynamicTable;

    protected $statuses;

    public function initialize()
    {
        $this->statuses = $this->usePackage(HrmsStatuses::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $this->view->status = $this->statuses->getById($this->getData()['id']);
            }

            $this->view->pick('statuses/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'hrms/statuses',
                    'remove'    => 'hrms/statuses/remove'
                ]
            ];

        $this->generateDTContent(
            $this->statuses,
            'hrms/statuses/view',
            null,
            ['name'],
            false,
            ['name'],
            $controlActions,
            [],
            null,
            'name'
        );

        $this->view->pick('statuses/list');
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

            $this->statuses->addStatus($this->postData());

            $this->view->responseCode = $this->statuses->packagesData->responseCode;

            $this->view->responseMessage = $this->statuses->packagesData->responseMessage;

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

            $this->statuses->updateStatus($this->postData());

            $this->view->responseCode = $this->statuses->packagesData->responseCode;

            $this->view->responseMessage = $this->statuses->packagesData->responseMessage;

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

            $this->statuses->removeStatus($this->postData());

            $this->view->responseCode = $this->statuses->packagesData->responseCode;

            $this->view->responseMessage = $this->statuses->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}