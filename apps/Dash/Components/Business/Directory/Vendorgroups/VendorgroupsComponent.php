<?php

namespace Apps\Dash\Components\Business\Directory\Vendorgroups;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Business\Directory\VendorGroups\VendorGroups;
use System\Base\BaseComponent;

class VendorgroupsComponent extends BaseComponent
{
    use DynamicTable;

    protected $vendorgroups;

    public function initialize()
    {
        $this->vendorgroups = $this->usePackage(VendorGroups::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {

                $group = $this->vendorgroups->getById($this->getData()['id']);

                $this->view->group = $group;
            }

            $this->view->pick('vendorgroups/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'business/directory/vendorgroups',
                    'remove'    => 'business/directory/vendorgroups/remove'
                ]
            ];

        $this->generateDTContent(
            $this->vendorgroups,
            'business/directory/vendorgroups/view',
            null,
            ['name'],
            true,
            ['name'],
            $controlActions,
            null,
            null,
            'name'
        );

        $this->view->pick('vendorgroups/list');
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

            $this->vendorgroups->addGroup($this->postData());

            $this->addResponse(
                $this->vendorgroups->packagesData->responseMessage,
                $this->vendorgroups->packagesData->responseCode
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

            $this->vendorgroups->updateGroup($this->postData());

            $this->addResponse(
                $this->vendorgroups->packagesData->responseMessage,
                $this->vendorgroups->packagesData->responseCode
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

            $this->vendorgroups->removeGroup($this->postData());

            $this->addResponse(
                $this->vendorgroups->packagesData->responseMessage,
                $this->vendorgroups->packagesData->responseCode
            );
        } else {
            $this->addResponse(
                'Method Not Allowed',
                1
            );
        }
    }
}