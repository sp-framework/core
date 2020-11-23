<?php

namespace Applications\Admin\Components\Emailservices;

use System\Base\BaseComponent;

class EmailservicesComponent extends BaseComponent
{
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $emailservices = $this->emailservices->init();

        $controlActions =
            [
                // 'disableActionsForIds'  => [1],
                'actionsToEnable'       =>
                [
                    'edit'      => 'emailservice',
                    'remove'    => 'emailservice/remove'
                ]
            ];

        if ($this->request->isPost()) {
            $replaceColumns =
                [
                    'encryption' =>
                        [
                            'html'  =>
                            [
                                '0' => '<span class="badge badge-secondary text-uppercase">Disabled</span>',
                                '1' => '<span class="badge badge-success text-uppercase">Enabled</span>'
                            ]
                        ]
                ];
        } else {
            $replaceColumns = null;
        }

        $this->generateDTContent(
            $emailservices,
            'emailservices/view',
            null,
            ['name', 'host', 'port', 'encryption'],
            false,
            ['name', 'host', 'port', 'encryption'],
            $controlActions,
            null,
            $replaceColumns,
            'name'
        );
    }
}