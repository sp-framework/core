<?php

namespace Applications\Ecom\Admin\Components\Users\Employees;

use Applications\Ecom\Common\Packages\Employees\Employees;
use Applications\Ecom\Dashboard\Packages\AdminLTETags\Traits\DynamicTable;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Storages;

class EmployeesComponent extends BaseComponent
{
    use DynamicTable;

    protected $employees;

    public function initialize()
    {
        $this->employees = $this->usePackage(Employees::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $this->view->employee = $this->employees->getById($this->getData()['id']);
            }

            $this->view->pick('employees/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'view'      => 'users/employees'
                ]
            ];

        $this->generateDTContent(
            $this->employees,
            'users/employees/view',
            null,
            ['employee_id', 'first_name', 'last_name'],
            true,
            ['employee_id', 'first_name', 'last_name'],
            $controlActions,
            [],
            $replaceColumns,
            'first_name'
        );

        $this->view->pick('employees/list');
    }

    public function getAllEmployeesAction()
    {
        $this->view->employees = $this->employees->getAll()->employees;
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

            $this->employees->addEmployees($this->postData());

            $this->view->responseCode = $this->employees->packagesData->responseCode;

            $this->view->responseMessage = $this->employees->packagesData->responseMessage;

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

            $this->employees->updateEmployees($this->postData());

            $this->view->responseCode = $this->employees->packagesData->responseCode;

            $this->view->responseMessage = $this->employees->packagesData->responseMessage;

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

            $this->employees->removeEmployees($this->postData());

            $this->view->responseCode = $this->employees->packagesData->responseCode;

            $this->view->responseMessage = $this->employees->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}