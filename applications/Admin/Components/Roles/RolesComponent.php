<?php

namespace Applications\Admin\Components\Roles;

use System\Base\BaseComponent;

class RolesComponent extends BaseComponent
{
    public function viewAction()
    {
        $roles = $this->roles->init();

        $controlActions =
            [
                // 'disableActionsForIds'  => [1],
                'actionsToEnable'       =>
                [
                    'edit'      => 'role',
                    'remove'    => 'role/remove'
                ]
            ];

        // if ($this->request->isPost()) {
        //     $rolesIdToName = [];
        //     foreach ($this->roles->getAll()->roles as $roleKey => $roleValue) {
        //         $rolesIdToName[$roleValue['id']] = $roleValue['name'];
        //     }

        //     $replaceColumns =
        //         [
        //             'parent_id' => ['html'  => $rolesIdToName]
        //         ];
        // } else {
        //     $replaceColumns = null;
        // }

        $this->generateDTContent(
            $roles,
            'roles/view',
            null,
            ['name', 'description'],
            true,
            ['name', 'description'],
            $controlActions,
            [],
            null,
            'name'
        );
    }
}