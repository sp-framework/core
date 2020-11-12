<?php

namespace Applications\Admin\Components\Users;

use System\Base\BaseComponent;

class UsersComponent extends BaseComponent
{
    public function viewAction()
    {
        $users = $this->users->init();

        $actions =
            [
                'view'      => 'user',
                'edit'      => 'user/edit',
                'remove'    => 'user/remove'
            ];

        $this->generateDTContent($users, 'users/view', null, [], true, [], $actions);
    }
}