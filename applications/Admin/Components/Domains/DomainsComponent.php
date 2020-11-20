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

        if ($this->request->isPost()) {
            $applicationsIdToName = [];
            foreach ($this->modules->applications->applications as $applicationKey => $applicationValue) {
                $applicationsIdToName[$applicationValue['id']] = $applicationValue['name'];
            }

            $replaceColumns =
                [
                    'default_application_id' => ['html'  => $applicationsIdToName]
                ];
        } else {
            $replaceColumns = null;
        }

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
            ['name', 'default_application_id', 'description'],
            false,
            ['name', 'default_application_id', 'description'],
            $controlActions,
            ['default_application_id' => 'Default Application'],
            $replaceColumns,
            'name'
        );
    }
}