<?php

namespace Applications\Admin\Components\Users;

use System\Base\BaseComponent;

class UsersComponent extends BaseComponent
{
    public function viewAction()
    {
        $users = $this->users->init();

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'user',
                    'remove'    => 'user/remove'
                ]
            ];

        $this->generateDTContent($users, 'users/view', null, ['email'], true, ['email'], $controlActions, null, null, 'email');
    }
}