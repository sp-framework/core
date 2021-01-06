<?php

namespace Applications\Dash\Components\Hrms\Employees\Tools\EmployeesStatuses;

use Applications\Dash\Packages\Hrms\Employees\Employees;
use Applications\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class EmployeesStatusesComponent extends BaseComponent
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
        // $this->links->url('storages/q/uuid/' . $employee['image'] . '/w/200');
        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'view'      => 'hrms/employees'
                ]
            ];

        $this->generateDTContent(
            $this->employees,
            'hrms/employees/view',
            null,
            ['full_name', 'designation'],
            true,
            ['full_name', 'designation'],
            $controlActions,
            [],
            null,
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

    public function searchAccountAction()
    {
        if ($this->request->isPost()) {
            if ($this->postData()['search']) {
                $searchQuery = $this->postData()['search'];

                if (strlen($searchQuery) < 3) {
                    return;
                }

                $searchAccounts = $this->accounts->searchAccountInternal($searchQuery);

                if ($searchAccounts) {
                    $this->view->responseCode = $this->accounts->packagesData->responseCode;

                    $this->view->accounts = $this->accounts->packagesData->accounts;
                }
            } else {
                $this->view->responseCode = 1;

                $this->view->responseMessage = 'search query missing';
            }
        }
    }
}