<?php

namespace Applications\Ecom\Admin\Components\Users\Employees;

use Applications\Ecom\Dashboard\Packages\AdminLTETags\Traits\DynamicTable;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Storages;

class EmployeessComponent extends BaseComponent
{
    use DynamicTable;

    protected $employees;

    public function initialize()
    {
        $this->employees = $this->usePackage(Employeess::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            $this->view->brands = $this->usePackage(Brands::class)->getAll()->brands;

            if ($this->getData()['id'] != 0) {

                $employee = $this->employees->getById($this->getData()['id']);

                $storages = $this->basepackages->storages;

                if ($employee['image'] && $employee['image'] !== '') {
                    $this->view->imageLink = $storages->getPublicLink($employee['image'], 200);
                }

                if ($employee['brands']) {
                    $employee['brands'] = Json::decode($employee['brands'], true);
                }

                $this->view->employeeType = $employee['type'];

                $this->view->employee = $employee;
            } else {
                $this->view->employeeType = $this->getData()['type'];
            }


            $this->view->pick('employees/view');

            return;
        }

        if ($this->request->isPost()) {
            $replaceColumns =
                [
                    'is_manufacturer'   => ['html'  =>
                        [
                            '0' => 'No',
                            '1' => 'Yes'
                        ]
                    ],
                    'does_dropship'   => ['html'  =>
                        [
                            '0' => 'No',
                            '1' => 'Yes'
                        ]
                    ]
                ];
        } else {
            $replaceColumns = null;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'inventory/employees',
                    'remove'    => 'inventory/employees/remove'
                ]
            ];

        $this->generateDTContent(
            $this->employees,
            'inventory/employees/view',
            null,
            ['abn', 'name', 'is_manufacturer', 'does_dropship'],
            true,
            ['abn', 'name', 'is_manufacturer', 'does_dropship'],
            $controlActions,
            [],
            $replaceColumns,
            'name'
        );

        $this->view->pick('employees/list');
    }

    public function getAllEmployeessAction()
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