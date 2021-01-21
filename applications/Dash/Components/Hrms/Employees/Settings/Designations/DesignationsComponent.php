<?php

namespace Applications\Dash\Components\Hrms\Employees\Settings\Designations;

use Applications\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Applications\Dash\Packages\Hrms\Employees\Settings\Designations\EmployeesDesignations;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class DesignationsComponent extends BaseComponent
{
    use DynamicTable;

    protected $designations;

    public function initialize()
    {
        $this->designations = $this->usePackage(EmployeesDesignations::class);
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
                    'edit'      => 'hrms/employees/settings/designations',
                    'remove'    => 'hrms/employees/settings/designations/remove'
                ]
            ];

        $this->generateDTContent(
            $this->designations,
            'hrms/employees/settings/designations/view',
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

            $this->designations->addEmployeesDesignation($this->postData());

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

            $this->designations->updateEmployeesDesignation($this->postData());

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

            $this->designations->removeEmployeesDesignation($this->postData());

            $this->view->responseCode = $this->designations->packagesData->responseCode;

            $this->view->responseMessage = $this->designations->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}