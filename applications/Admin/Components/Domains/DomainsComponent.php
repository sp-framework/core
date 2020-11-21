<?php

namespace Applications\Admin\Components\Domains;

use System\Base\BaseComponent;

class DomainsComponent extends BaseComponent
{
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $domains = $this->modules->domains->init();

        $controlActions =
            [
                // 'disableActionsForIds'  => [1],
                'actionsToEnable'       =>
                [
                    'edit'      => 'domain',
                    'remove'    => 'domain/remove'
                ]
            ];

        $this->generateDTContent(
            $domains,
            'domains/view',
            null,
            ['name'],
            false,
            ['name'],
            $controlActions,
            null,
            null,
            'name'
        );
    }
}