<?php

namespace Applications\Ecom\Admin\Components\Users\Customers;

use Applications\Ecom\Admin\Packages\AdminLTETags\Traits\DynamicTable;
use Applications\Ecom\Admin\Packages\Customers\Customers;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Storages;

class CustomersComponent extends BaseComponent
{
    use DynamicTable;

    protected $customers;

    public function initialize()
    {
        $this->customers = $this->usePackage(Customers::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            $this->view->brands = $this->usePackage(Brands::class)->getAll()->brands;

            if ($this->getData()['id'] != 0) {

                $customer = $this->customers->getById($this->getData()['id']);

                $storages = $this->basepackages->storages;

                if ($customer['image'] && $customer['image'] !== '') {
                    $this->view->imageLink = $storages->getPublicLink($customer['image'], 200);
                }

                if ($customer['brands']) {
                    $customer['brands'] = Json::decode($customer['brands'], true);
                }

                $this->view->customerType = $customer['type'];

                $this->view->customer = $customer;
            } else {
                $this->view->customerType = $this->getData()['type'];
            }


            $this->view->pick('customers/view');

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
                    'edit'      => 'inventory/customers',
                    'remove'    => 'inventory/customers/remove'
                ]
            ];

        $this->generateDTContent(
            $this->customers,
            'inventory/customers/view',
            null,
            ['abn', 'name', 'is_manufacturer', 'does_dropship'],
            true,
            ['abn', 'name', 'is_manufacturer', 'does_dropship'],
            $controlActions,
            [],
            $replaceColumns,
            'name'
        );

        $this->view->pick('customers/list');
    }

    public function CustomersAction()
    {
        $this->view->customers = $this->customers->getAll()->customers;
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

            $this->customers->Customer($this->postData());

            $this->view->responseCode = $this->customers->packagesData->responseCode;

            $this->view->responseMessage = $this->customers->packagesData->responseMessage;

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

            $this->customers->Customer($this->postData());

            $this->view->responseCode = $this->customers->packagesData->responseCode;

            $this->view->responseMessage = $this->customers->packagesData->responseMessage;

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

            $this->customers->Customer($this->postData());

            $this->view->responseCode = $this->customers->packagesData->responseCode;

            $this->view->responseMessage = $this->customers->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}