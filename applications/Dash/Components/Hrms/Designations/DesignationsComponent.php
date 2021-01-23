<?php

namespace Applications\Dash\Components\Hrms\Designations;

use Applications\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Applications\Dash\Packages\Hrms\Designations\HrmsDesignations;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class DesignationsComponent extends BaseComponent
{
    use DynamicTable;

    protected $designations;

    public function initialize()
    {
        $this->designations = $this->usePackage(HrmsDesignations::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $this->view->status = $this->designations->getById($this->getData()['id']);
            }

            $this->view->pick('designations/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'hrms/designations',
                    'remove'    => 'hrms/designations/remove'
                ]
            ];

        $this->generateDTContent(
            $this->designations,
            'hrms/designations/view',
            null,
            ['name'],
            false,
            ['name'],
            $controlActions,
            [],
            null,
            'name'
        );

        $this->view->pick('designations/list');
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

            $this->designations->addDesignation($this->postData());

            $this->view->responseCode = $this->designations->packagesData->responseCode;

            $this->view->responseMessage = $this->designations->packagesData->responseMessage;

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

            $this->designations->updateDesignation($this->postData());

            $this->view->responseCode = $this->designations->packagesData->responseCode;

            $this->view->responseMessage = $this->designations->packagesData->responseMessage;

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

            $this->designations->removeDesignation($this->postData());

            $this->view->responseCode = $this->designations->packagesData->responseCode;

            $this->view->responseMessage = $this->designations->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}