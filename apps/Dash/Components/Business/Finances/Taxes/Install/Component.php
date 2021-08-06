<?php

namespace Apps\Dash\Components\Business\Finances\Taxes\Install;

use Apps\Dash\Components\Business\Finances\Taxes\TaxesComponent;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class Component extends BaseComponent
{
    public function installComponent()
    {
        if ($this->checkComponent(TaxesComponent::class)) {

            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Module already installed. Either update or reinstall';

            return;
        }

        $this->registerComponent();
    }

    protected function registerComponent()
    {
        $componentPath = '/apps/Dash/Components/Business/Finances/Taxes/';

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
            'app_type'              => $jsonFile['app_type'],
            'route'                 => $jsonFile['route'],
            'description'           => $jsonFile['description'],
            'category'              => $jsonFile['category'],
            'sub_category'          => $jsonFile['sub_category'],
            'version'               => $jsonFile['version'],
            'class'                 => $jsonFile['class'],
            'repo'                  => $jsonFile['repo'],
            'installed'             => '1',
            'updated_by'            => $this->auth->account()['id'],
            'settings'              =>
                isset($jsonFile['settings']) ?
                Json::encode($jsonFile['settings']) :
                null,
            'dependencies'          =>
                isset($jsonFile['dependencies']) ?
                Json::encode($jsonFile['dependencies']) :
                null,
            'menu'                  =>
                isset($jsonFile['menu']) ?
                Json::encode($jsonFile['menu']) :
                false,
            'apps'                  =>
                Json::encode([$this->app['id']=>['installed'=>true,'menu_id'=>$menu['id']]]),
            'files'                 => Json::encode($this->getInstalledFiles($componentPath))
        ];

        $this->modules->components->add($component);
        $this->logger->log->info('Component ' . $component['name'] . ' installed successfully on app ' . $this->app['name']);
    }

    protected function registerMenu($menu)
    {
        $sequence = $menu['seq'];
        unset($menu['seq']);
        $menu['menu'] = Json::encode($menu);

        $menu['apps'] = Json::encode([$this->app['id']=>['enabled'=> true]]);
        $menu['sequence'] = $sequence;

        $this->basepackages->menus->add($menu);

        return $this->basepackages->menus->packagesData->last;
    }
}