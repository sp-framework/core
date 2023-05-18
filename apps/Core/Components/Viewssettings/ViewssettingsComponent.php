<?php

namespace Apps\Core\Components\Viewssettings;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class ViewssettingsComponent extends BaseComponent
{
    use DynamicTable;

    public function initialize()
    {
        //
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $viewssettings = $this->modules->viewsSettings->getViewsSettingsById($this->getData()['id']);

                if (!$viewssettings) {
                    return $this->throwIdNotFound();
                }
            }

            if (isset($viewssettings)) {
                $viewssettings['settings'] = Json::decode($viewssettings['settings'], true);
                $viewssettings = $this->replaceColumns([$viewssettings])[0];
                $this->view->viewssettings = $viewssettings;
            } else {
                $this->view->viewssettings = [];
            }

            $this->view->domains = $this->domains->domains;

            $appsArr = $this->apps->apps;
            $apps = [];

            foreach ($appsArr as $key => $value) {
                $apps[$value['id']]['id'] = $value['id'];
                $apps[$value['id']]['name'] = $value['name'];
                $views = $this->modules->views->getViewsForAppId($value['id']);

                if ($views) {
                    foreach ($views as $viewKey => $view) {
                        $apps[$value['id']]['views'][$view['id']]['id'] = $view['id'];
                        $apps[$value['id']]['views'][$view['id']]['name'] = $view['display_name'];
                        $apps[$value['id']]['views'][$view['id']]['settings'] = Json::decode($view['settings'], true);
                    }
                }
            }

            $this->view->apps = $apps;

            $this->view->pick('viewssettings/view');

            return;
        }

        $controlActions =
            [
                // 'disableActionsForIds'  => [1],
                'actionsToEnable'       =>
                [
                    'edit'      => 'viewssettings',
                    'remove'    => 'viewssettings/remove'
                ]
            ];

        $replaceColumns =
            function ($dataArr) {
                if ($dataArr && is_array($dataArr) && count($dataArr) > 0) {
                    return $this->replaceColumns($dataArr);
                }

                return $dataArr;
            };

        $this->generateDTContent(
            $this->modules->viewsSettings,
            'viewssettings/view',
            null,
            ['domain_id', 'app_id', 'view_id'],
            true,
            ['domain_id', 'app_id', 'view_id'],
            $controlActions,
            ['domain_id' => 'Domain', 'app_id' => 'App', 'view_id' => 'View'],
            $replaceColumns,
            'id'
        );

        $this->view->pick('viewssettings/list');
    }

    protected function replaceColumns($dataArr)
    {
        foreach ($dataArr as $dataKey => &$data) {
            $data = $this->formatDomainId($dataKey, $data);
            $data = $this->formatAppId($dataKey, $data);
            $data = $this->formatViewId($dataKey, $data);
        }

        return $dataArr;
    }

    protected function formatDomainId($rowId, $data)
    {
        if ($data['domain_id'] == '0') {
            $data['domain_id'] = '<span class="badge badge-danger text-uppercase">ERROR: NO DOMAIN</span>';
        } else {
            $domain = $this->domains->getDomainById($data['domain_id']);

            if ($domain) {
                $data['domain_id'] = '<span class="text-uppercase">' . $domain['name'] . '</span>';
            } else {
                $data['domain_id'] = '<span class="badge badge-danger text-uppercase">ERROR: NO DOMAIN</span>';
            }
        }

        return $data;
    }

    protected function formatAppId($rowId, $data)
    {
        if ($data['app_id'] == '0') {
            $data['app_id'] = '<span class="badge badge-danger text-uppercase">ERROR: NO DOMAIN</span>';
        } else {
            $app = $this->apps->getAppById($data['app_id']);

            if ($app) {
                $data['app_id'] = '<span class="text-uppercase">' . $app['name'] . '</span>';
            } else {
                $data['app_id'] = '<span class="badge badge-danger text-uppercase">ERROR: NO DOMAIN</span>';
            }
        }

        return $data;
    }

    protected function formatViewId($rowId, $data)
    {
        if ($data['view_id'] == '0') {
            $data['view_id'] = '<span class="badge badge-danger text-uppercase">ERROR: NO DOMAIN</span>';
        } else {
            $view = $this->modules->views->getViewById($data['view_id']);

            if ($view) {
                $data['view_id'] = '<span class="text-uppercase">' . $view['display_name'] . '</span>';
            } else {
                $data['view_id'] = '<span class="badge badge-danger text-uppercase">ERROR: NO DOMAIN</span>';
            }
        }

        return $data;
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

            $this->modules->viewsSettings->addViewsSettings($this->postData());

            $this->addResponse(
                $this->modules->viewsSettings->packagesData->responseMessage,
                $this->modules->viewsSettings->packagesData->responseCode
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

            $this->modules->viewsSettings->updateViewsSettings($this->postData());

            $this->addResponse(
                $this->modules->viewsSettings->packagesData->responseMessage,
                $this->modules->viewsSettings->packagesData->responseCode
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
            if (!$this->checkCSRF()) {
                return;
            }

            $this->modules->viewsSettings->removeViewsSettings($this->postData());

            $this->addResponse(
                $this->modules->viewsSettings->packagesData->responseMessage,
                $this->modules->viewsSettings->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function getViewsSettingsByViewIdDomainIdAndAppIdAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->postData()['domain_id'] &&
                $this->postData()['app_id'] &&
                $this->postData()['view_id']
            ) {
                $viewsSettings =
                    $this->modules->viewsSettings->getViewsSettingsByViewIdDomainIdAndAppId(
                        $this->postData()['domain_id'],
                        $this->postData()['app_id'],
                        $this->postData()['view_id']
                    );

                if ($viewsSettings) {
                    $this->addResponse('Settings for this view is already defined.', 1);

                    return;
                }

                $this->addResponse('No settings for this view found.');
            }
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function getViewsSettingsFromViewModuleAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if (isset($this->postData()['viewsettings_id']) || isset($this->postData()['view_id'])) {
                $viewsSettings = $this->modules->viewsSettings->getViewsSettingsFromViewModule($this->postData());

                if ($viewsSettings) {
                    $this->addResponse(
                        $this->modules->viewsSettings->packagesData->responseMessage,
                        0,
                        $this->modules->viewsSettings->packagesData->responseData,
                    );

                    return;
                }

                $this->addResponse('No settings for this view found.', 1, []);
            }
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}