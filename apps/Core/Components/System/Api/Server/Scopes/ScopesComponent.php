<?php

namespace Apps\Core\Components\System\Api\Server\Scopes;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use System\Base\BaseComponent;

class ScopesComponent extends BaseComponent
{
    use DynamicTable;

    protected $apiScopes;

    public function initialize()
    {
        $this->apiScopes = $this->api->init()->scopes;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $scope = $this->apiScopes->generateViewData($this->getData()['id']);

                if (!$scope) {
                    return $this->throwIdNotFound();
                }
            } else {
                $scope = $this->apiScopes->generateViewData();
            }

            if ($scope) {
                $this->view->components = $this->apiScopes->packagesData->components;

                $this->view->acls = $this->apiScopes->packagesData->acls;

                $this->view->scope = $this->apiScopes->packagesData->scope;

                $this->view->apps = $this->apiScopes->packagesData->apps;

                $this->view->scopes = $this->apiScopes->packagesData->scopes;
            }

            $this->addResponse(
                $this->apiScopes->packagesData->responseMessage,
                $this->apiScopes->packagesData->responseCode
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
            $this->apiScopes,
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

        $this->apiScopes->addScope($this->postData());

        $this->addResponse(
            $this->apiScopes->packagesData->responseMessage,
            $this->apiScopes->packagesData->responseCode
        );
    }

    /**
     * @acl(name="update")
     */
    public function updateAction()
    {
        $this->requestIsPost();

        $this->apiScopes->updateScope($this->postData());

        $this->addResponse(
            $this->apiScopes->packagesData->responseMessage,
            $this->apiScopes->packagesData->responseCode
        );
    }

    /**
     * @acl(name="remove")
     */
    public function removeAction()
    {
        $this->requestIsPost();

        $this->apiScopes->removeScope($this->postData());

        $this->addResponse(
            $this->apiScopes->packagesData->responseMessage,
            $this->apiScopes->packagesData->responseCode
        );
    }

    public function generateScopeNameAction()
    {
        $this->requestIsPost();

        $this->apiScopes->extractScopeName($this->postData());

        $this->addResponse(
            $this->apiScopes->packagesData->responseMessage,
            $this->apiScopes->packagesData->responseCode,
            $this->apiScopes->packagesData->responseData
        );
    }
}