<?php

namespace Apps\Core\Components\System\Api\Server\Scopes;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use System\Base\BaseComponent;

class ScopesComponent extends BaseComponent
{
    use DynamicTable;

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $scope = $this->api->scopes->generateViewData($this->getData()['id']);

                if (!$scope) {
                    return $this->throwIdNotFound();
                }
            } else {
                $scope = $this->api->scopes->generateViewData();
            }

            if ($scope) {
                $this->view->components = $this->api->scopes->packagesData->components;

                $this->view->acls = $this->api->scopes->packagesData->acls;

                $this->view->scope = $this->api->scopes->packagesData->scope;

                $this->view->apps = $this->api->scopes->packagesData->apps;

                $this->view->scopes = $this->api->scopes->packagesData->scopes;
            }

            $this->addResponse(
                $this->api->scopes->packagesData->responseMessage,
                $this->api->scopes->packagesData->responseCode
            );

            $this->view->pick('scopes/view');

            return;
        }

        $controlActions =
            [
                // 'disableActionsForIds'  => [1],
                'actionsToEnable'       =>
                [
                    'edit'      => 'system/api/server/scopes',
                    'remove'    => 'system/api/server/scopes/remove'
                ]
            ];

        $this->generateDTContent(
            $this->api->scopes,
            'system/api/server/scopes/view',
            null,
            ['name', 'scope_name'],
            true,
            ['name', 'scope_name'],
            $controlActions,
            [],
            null,
            'name'
        );

        $this->view->pick('scopes/list');
    }

    /**
     * @acl(name="add")
     */
    public function addAction()
    {
        $this->requestIsPost();

        $this->api->scopes->addScope($this->postData());

        $this->addResponse(
            $this->api->scopes->packagesData->responseMessage,
            $this->api->scopes->packagesData->responseCode
        );
    }

    /**
     * @acl(name="update")
     */
    public function updateAction()
    {
        $this->requestIsPost();

        $this->api->scopes->updateScope($this->postData());

        $this->addResponse(
            $this->api->scopes->packagesData->responseMessage,
            $this->api->scopes->packagesData->responseCode
        );
    }

    /**
     * @acl(name="remove")
     */
    public function removeAction()
    {
        $this->requestIsPost();

        $this->api->scopes->removeScope($this->postData());

        $this->addResponse(
            $this->api->scopes->packagesData->responseMessage,
            $this->api->scopes->packagesData->responseCode
        );
    }

    public function generateScopeNameAction()
    {
        $this->requestIsPost();

        $this->api->scopes->extractScopeName($this->postData());

        $this->addResponse(
            $this->api->scopes->packagesData->responseMessage,
            $this->api->scopes->packagesData->responseCode,
            $this->api->scopes->packagesData->responseData
        );
    }
}