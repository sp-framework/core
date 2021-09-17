<?php

namespace Apps\Dash\Components\Crms\Customergroups;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Crms\CustomerGroups\CustomerGroups;
use System\Base\BaseComponent;

class CustomergroupsComponent extends BaseComponent
{
    use DynamicTable;

    protected $customergroups;

    public function initialize()
    {
        $this->customergroups = $this->usePackage(CustomerGroups::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {

                $customerGroup = $this->customergroups->getById($this->getData()['id']);

                if (!$customerGroup) {
                    return $this->throwIdNotFound();
                }

                $this->view->customerGroup = $customerGroup;
            }

            $this->view->pick('customergroups/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'crms/customergroups',
                    'remove'    => 'crms/customergroups/remove'
                ]
            ];

        $this->generateDTContent(
            $this->customergroups,
            'crms/customergroups/view',
            null,
            ['name'],
            true,
            ['name'],
            $controlActions,
            null,
            null,
            'name'
        );

        $this->view->pick('customergroups/list');
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

            $this->customergroups->addCustomerGroup($this->postData());

            $this->addResponse(
                $this->customergroups->packagesData->responseMessage,
                $this->customergroups->packagesData->responseCode
            );
        } else {
            $this->addResponse(
                'Method Not Allowed',
                1
            );
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

            $this->customergroups->updateCustomerGroup($this->postData());

            $this->addResponse(
                $this->customergroups->packagesData->responseMessage,
                $this->customergroups->packagesData->responseCode
            );
        } else {
            $this->addResponse(
                'Method Not Allowed',
                1
            );
        }
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        if ($this->request->isPost()) {

            $this->customergroups->removeCustomerGroup($this->postData());

            $this->addResponse(
                $this->customergroups->packagesData->responseMessage,
                $this->customergroups->packagesData->responseCode
            );
        } else {
            $this->addResponse(
                'Method Not Allowed',
                1
            );
        }
    }
}