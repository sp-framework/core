<?php

namespace System\Base\Providers\ApiServiceProvider;

use System\Base\BasePackage;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiScopes;

class Scopes extends BasePackage
{
    protected $modelToUse = ServiceProviderApiScopes::class;

    protected $packageName = 'scopes';

    public $scopes;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        return $this;
    }

    /**
     * @notification(name=add)
     */
    public function addScope(array $data)
    {
        if (!isset($data['scope_name']) ||
            (isset($data['scope_name']) && $data['scope_name'] === '')
        ) {
            $data = $this->extractScopeName($data);
        }

        if ($this->add($data)) {
            $this->addResponse('Added ' . $data['name'] . ' scope');
        } else {
            $this->addResponse('Error adding new scope.', 1);
        }
    }

    /**
     * @notification(name=update)
     */
    public function updateScope(array $data)
    {
        if (!isset($data['scope_name']) ||
            (isset($data['scope_name']) && $data['scope_name'] === '')
        ) {
            $data = $this->extractScopeName($data);
        }

        if ($this->update($data)) {
            $this->addResponse('Updated ' . $data['name'] . ' scope');
        } else {
            $this->addResponse('Error updating scope.', 1);
        }
    }

    public function extractScopeName($data)
    {
        if ($data['name'] === '') {
            $data['name'] = $this->secTools->random->base58(6);
        }

        $data['scope_name'] = str_replace(' ', '', strtolower($data['name']));

        $this->addResponse('Generated scope name', 0, $data);

        return $data;
    }

    /**
     * @notification(name=remove)
     */
    public function removeScope(array $data)
    {
        if (isset($data['id'])) {
            $hasApi = false;

            if ($this->config->databasetype === 'db') {
                $scopeObj = $this->getFirst('id', $data['id']);

                if ($scopeObj->getApi() && $scopeObj->getApi()->count() > 0) {
                    $hasApi = true;
                }
            } else {
                $this->setFFRelations(true);

                $scope = $this->getById($data['id']);

                if (isset($scope['api']) && is_array($scope['api']) && count($scope['api']) > 0) {
                    $hasApi = true;
                }
            }

            if ($hasApi) {
                $this->addResponse('Scope has api assigned to it. Cannot removes scope.', 1);

                return false;
            }

            if ($this->remove($data['id'], true, false)) {
                $this->addResponse('Removed scope');
            } else {
                $this->addResponse('Error removing scope.', 1);
            }
        } else {
            $this->addResponse('Error removing scope.', 1);
        }
    }

    public function generateViewData(int $rid = null)
    {
        $acls = [];

        $appsArr = $this->apps->apps;

        foreach ($appsArr as $appKey => $app) {
            $componentsArr = msort($this->modules->components->getComponentsForAppId($app['id']), 'name');

            if (count($componentsArr) > 0) {
                $components[strtolower($app['id'])] =
                    [
                        'title' => strtoupper($app['name']),
                        'id' => strtoupper($app['id'])
                    ];
                foreach ($componentsArr as $key => $component) {
                    $reflector = $this->annotations->get($component['class']);
                    $methods = $reflector->getMethodsAnnotations();

                    if ($methods && count($methods) > 2 && isset($methods['apiViewAction'])) {
                        $components[strtolower($app['id'])]['childs'][$key]['id'] = $component['id'];
                        $components[strtolower($app['id'])]['childs'][$key]['title'] = $component['name'];
                    }
                }
            }
        }

        $this->packagesData->components = $components;

        $scopesArr = $this->getAll()->scopes;
        $scopes = [];
        foreach ($scopesArr as $scopeKey => $scopeValue) {
            $scopes[$scopeValue['id']] =
                [
                    'id'    => $scopeValue['id'],
                    'name'  => $scopeValue['name']
                ];
        }

        if ($rid) {
            $scope = $this->getById($rid);

            if ($scope) {
                if ($scope['permissions'] && $scope['permissions'] !== '') {
                    if (is_string($scope['permissions'])) {
                        $permissionsArr = $this->helper->decode($scope['permissions'], true);
                    } else {
                        $permissionsArr = $scope['permissions'];
                    }
                } else {
                    $permissionsArr = [];
                }
                $permissions = [];

                foreach ($appsArr as $appKey => $app) {
                    $componentsArr = msort($this->modules->components->getComponentsForAppId($app['id']), 'name');

                    foreach ($componentsArr as $key => $component) {
                        if ($component['class'] && $component['class'] !== '') {
                            $reflector = $this->annotations->get($component['class']);
                            $methods = $reflector->getMethodsAnnotations();

                            if ($methods && count($methods) > 2 && isset($methods['apiViewAction'])) {
                                foreach ($methods as $annotation) {
                                    if ($annotation->getAll('api_acl')) {
                                        $action = $annotation->getAll('api_acl')[0]->getArguments();
                                        $acls[$action['name']] = $action['name'];
                                        if (isset($permissionsArr[$app['id']][$component['id']])) {
                                            $permissions[$app['id']][$component['id']] = $permissionsArr[$app['id']][$component['id']];
                                        } else {
                                            $permissions[$app['id']][$component['id']][$action['name']] = 0;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $this->packagesData->acls = $this->helper->encode($acls);

                $scope['permissions'] = $this->helper->encode($permissions);

                $this->packagesData->scope = $scope;
            } else {

                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Scope Not Found!';

                return;
            }
        } else {
            $scope = [];
            $permissions = [];

            foreach ($appsArr as $appKey => $app) {
                $componentsArr = msort($this->modules->components->getComponentsForAppId($app['id']), 'name');

                foreach ($componentsArr as $key => $component) {
                    //Build ACL Columns
                    if ($component['class'] && $component['class'] !== '') {
                        $reflector = $this->annotations->get($component['class']);
                        $methods = $reflector->getMethodsAnnotations();

                        if ($methods && count($methods) > 2 && isset($methods['viewAction'])) {
                            foreach ($methods as $annotation) {
                                if ($annotation->getAll('api_acl')) {
                                    $action = $annotation->getAll('api_acl')[0]->getArguments();
                                    $acls[$action['name']] = $action['name'];
                                    $permissions[$app['id']][$component['id']][$action['name']] = 0;
                                }
                            }
                        }
                    }
                }
            }

            $this->packagesData->acls = $this->helper->encode($acls);
            $scope['permissions'] = $this->helper->encode($permissions);
            $this->packagesData->scope = $scope;
        }

        $this->packagesData->apps = $appsArr;

        $this->packagesData->scopes = $scopes;

        return true;
    }

    public function getScopeByScopeName($scope)
    {
        $scope = $this->getFirst('scope_name', $scope, false, true, null, [], true);

        if ($scope) {
            return $scope;
        }

        return false;
    }
}