<?php

namespace Apps\Dash\Components\System\Users\Roles;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class RolesComponent extends BaseComponent
{
    use DynamicTable;

    protected $roles;

    public function initialize()
    {
        $this->roles = $this->basepackages->roles;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $role = $this->roles->generateViewData($this->getData()['id']);

                if (!$role) {
                    return $this->throwIdNotFound();
                }
            } else {
                $role = $this->roles->generateViewData();
            }

            if ($role) {
                $app = $this->apps->getAppInfo();

                $middlewares = $this->modules->middlewares->getMiddlewaresForAppType($app['app_type'],null);

                $middlewareEnabledForApps = [];

                $this->view->aclMiddlewareEnabled = false;

                foreach ($middlewares as $key => &$middleware) {
                    if ($middleware['name'] === 'Acl') {
                        if (isset($middleware['apps']) && is_string($middleware['apps'])) {
                            $middleware['apps'] = Json::decode($middleware['apps'], true);

                            foreach ($middleware['apps'] as $appId => $value) {
                                if (isset($value['enabled']) && $value['enabled'] === true) {
                                    array_push($middlewareEnabledForApps, $appId);
                                }
                            }
                        }
                    }
                }

                $components = $this->roles->packagesData->components;

                if (count($middlewareEnabledForApps) > 0) {
                    $this->view->aclMiddlewareEnabled = true;
                    foreach ($components as $key => $component) {
                        if (!in_array($component['id'], $middlewareEnabledForApps)) {
                            unset($components[$key]);
                        }
                    }
                }

                $this->view->components = $components;

                $this->view->acls = $this->roles->packagesData->acls;

                $this->view->role = $this->roles->packagesData->role;

                $this->view->apps = $this->roles->packagesData->apps;

                $this->view->roles = $this->roles->packagesData->roles;

                if ($this->getData()['id'] == 1) {
                    $this->view->canUpdate = false;
                }
            }

            $this->addResponse(
                $this->roles->packagesData->responseMessage,
                $this->roles->packagesData->responseCode
            );

            $this->view->pick('roles/view');

            return;
        }

        $controlActions =
            [
                // 'disableActionsForIds'  => [1],
                'actionsToEnable'       =>
                [
                    'edit'      => 'system/users/roles',
                    'remove'    => 'system/users/roles/remove'
                ]
            ];

        $this->generateDTContent(
            $this->roles,
            'system/users/roles/view',
            null,
            ['name', 'description'],
            true,
            ['name', 'description'],
            $controlActions,
            [],
            null,
            'name'
        );

        $this->view->pick('roles/list');
    }

    /**
     * @acl(name="add")
     */
    public function addAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->roles->addRole($this->postData());

            $this->addResponse(
                $this->roles->packagesData->responseMessage,
                $this->roles->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    /**
     * @acl(name="update")
     */
    public function updateAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->roles->updateRole($this->postData());

            $this->addResponse(
                $this->roles->packagesData->responseMessage,
                $this->roles->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    /**
     * @acl(name="remove")
     */
    public function removeAction()
    {
        if ($this->request->isPost()) {

            $this->roles->removeRole($this->postData());

            $this->addResponse(
                $this->roles->packagesData->responseMessage,
                $this->roles->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}