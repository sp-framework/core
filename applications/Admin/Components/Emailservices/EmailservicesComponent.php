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

        $this->generateDTContent(
            $emailservices,
            'emailservices/view',
            null,
            ['name', 'host', 'port', 'encryption'],
            false,
            ['name', 'host', 'port', 'encryption'],
            $controlActions,
            null,
            null,
            'name'
        );
    }
}