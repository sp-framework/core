<?php

namespace Applications\Admin\Components;

use System\Base\BaseComponent;

class UsersComponent extends BaseComponent
{
    public function viewAction()
    {
        $users = $this->users->init();

        $this->view->table = $users->getModelsColumnMap();

        // var_dump($users->getAll()->users);
        // $this->view->disable();
    }
}