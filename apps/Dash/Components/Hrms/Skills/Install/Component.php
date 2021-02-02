<?php

namespace Apps\Dash\Components\Hrms\Skills\Install;

use Apps\Dash\Components\Hrms\Skills\SkillsComponent;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class Component extends BaseComponent
{
    public function installComponent()
    {
        if ($this->checkComponent(SkillsComponent::class)) {

            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Module already installed. Either update or reinstall';

            return;
        }

        $this->registerComponent();
    }

    protected function registerComponent()
    {
        $componentPath = '/apps/Dash/Components/Hrms/Skills/';

        $jsonFile =
            Json::decode($this->localContent->read($componentPath . '/Install/component.json'), true);

        if (!$jsonFile) {
            throw new \Exception('Problem reading component.json at location ' . $componentPath);
        }

        if ($jsonFile['menu']) {
            $menu = $this->registerMenu($jsonFile['menu']);
        }

        $component =
            [
                'name'                  => $jsonFile['name'],
                'route'                 => $jsonFile['route'],
                'description'           => $jsonFile['description'],
                'app_type'              => $jsonFile['app_type'],
                'category'              => $jsonFile['category'],
                'sub_category'          => $jsonFile['sub_category'],
                'version'               => $jsonFile['version'],
                'class'                 => $jsonFile['class'],
                'repo'                  => $jsonFile['repo'],
                'dependencies'          =>
                    isset($jsonFile['dependencies']) ?
                    Json::encode($jsonFile['dependencies']) :
                    null,
                'menu'                  =>
                    isset($jsonFile['menu']) ?
                    Json::encode($jsonFile['menu']) :
                    false,
                'menu_id'               => $menu['id'],
                'installed'             => 1,
                'apps'          =>
                    Json::encode(['1'=>['enabled'=>true]]),
                'files'                 => Json::encode($this->getInstalledFiles($componentPath)),
                'settings'              =>
                    isset($jsonFile['settings']) ?
                    Json::encode($jsonFile['settings']) :
                    null,
                'updated_by'            => 0
            ];

        $this->modules->components->add($component);
        $this->logger->log->info('Component ' . $component['name'] . ' installed successfully on app ' . $this->app['name']);
    }

    protected function registerMenu($menu)
    {
        $sequence = $menu['seq'];
        unset($menu['seq']);
        $menu['menu'] = Json::encode($menu);

        $menu['app_id'] = $this->app['id'];
        $menu['sequence'] = $sequence;
        $menu['apps'] =
                Json::encode([$this->app['id']=>['enabled'=>true]]);

        $this->basepackages->menus->add($menu);

        return $this->basepackages->menus->packagesData->last;
    }
}