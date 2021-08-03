<?php

namespace Apps\Dash\Components\Business\Directory\Groups;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Business\Directory\Groups\Groups;
use System\Base\BaseComponent;

class GroupsComponent extends BaseComponent
{
    use DynamicTable;

    protected $groups;

    public function initialize()
    {
        $this->groups = $this->usePackage(Groups::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {

                $group = $this->groups->getById($this->getData()['id']);

                $this->view->group = $group;
            }

            $this->view->pick('groups/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'business/directory/groups',
                    'remove'    => 'business/directory/groups/remove'
                ]
            ];

        $this->generateDTContent(
            $this->groups,
            'business/directory/groups/view',
            null,
            ['name'],
            true,
            ['name'],
            $controlActions,
            null,
            null,
            'name'
        );

        $this->view->pick('groups/list');
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

            $this->groups->addGroup($this->postData());

            $this->addResponse(
                $this->groups->packagesData->responseMessage,
                $this->groups->packagesData->responseCode
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

            $this->groups->updateGroup($this->postData());

            $this->addResponse(
                $this->groups->packagesData->responseMessage,
                $this->groups->packagesData->responseCode
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

            $this->groups->removeGroup($this->postData());

            $this->addResponse(
                $this->groups->packagesData->responseMessage,
                $this->groups->packagesData->responseCode
            );
        } else {
            $this->addResponse(
                'Method Not Allowed',
                1
            );
        }
    }
}