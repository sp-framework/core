<?php

namespace Applications\Admin\Components\Users;

use System\Base\BaseComponent;

class UsersComponent extends BaseComponent
{
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $users = $this->users->init();

        if ($this->request->isPost()) {
            $rolesIdToName = [];
            foreach ($this->roles->getAll()->roles as $roleKey => $roleValue) {
                $rolesIdToName[$roleValue['id']] = $roleValue['name'] . ' (' . $roleValue['id'] . ')';
            }

            $replaceColumns =
                [
                    'role_id' => ['html'  => $rolesIdToName]
                ];
        } else {
            $replaceColumns = null;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'user',
                    'remove'    => 'user/remove'
                ]
            ];

        $this->generateDTContent(
            $users,
            'users/view',
            null,
            ['email', 'role_id'],
            true,
            ['email', 'role_id'],
            $controlActions,
            ['role_id' => 'role (ID)'],
            $replaceColumns,
            'email'
        );
    }
}