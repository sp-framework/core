<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use Phalcon\Tag;
use Phalcon\Assets\Inline;
use System\Base\BasePackage;
use Phalcon\Html\Helper\Doctype;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesViews;

class Views extends BasePackage
{
    protected $modelToUse = ModulesViews::class;

    public $views;

    protected $view;

    protected $voltCompiledPath;

    protected $phalconViewPath;

    protected $phalconViewLayoutPath;

    protected $phalconViewLayoutFile;

    protected $activeLayout;

    protected $viewSettings;

    protected $cache;

    protected $tags;

    protected $assetsPath;

    protected $assetsCollections;

    protected $componentName;

    protected $assetsVersion;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        $this->setApp();

        $this->setVoltCompiledPath();

        $this->setPhalconViewPath();

        $this->setPhalconViewLayoutPath();

        $this->setPhalconViewLayoutFile();

        $this->setAssetsPath();

        return $this;
    }

    public function setVoltCompiledPath($path = null)
    {
        if ($path) {
            $this->voltCompiledPath = $path;
            return;
        }

        if (!isset($this->voltCompiledPath)) {
            if ($this->app && $this->view) {
                $this->voltCompiledPath =
                    base_path('apps/' .
                              ucfirst($this->app['app_type']) .
                              '/Views/Html_compiled/' . ucfirst($this->app['route']) . '/' . $this->view['name'] . '/'
                          );
            } else {
                $this->voltCompiledPath =
                    base_path('system/Base/Providers/ErrorServiceProvider/View/Html_compiled/');
            }

            if (!is_dir($this->voltCompiledPath)) {
                mkdir(
                    $this->voltCompiledPath,
                    0777,
                    true
                );
            }
        }
    }

    public function setPhalconViewPath($path = null)
    {
        if ($path) {
            $this->phalconViewPath = $path;
            return;
        }

        if (!isset($this->phalconViewPath)) {
            if ($this->app && $this->view) {
                $this->phalconViewPath =
                    base_path('apps/' .
                              ucfirst($this->app['app_type']) .
                              '/Views/' . $this->view['name'] .
                              '/html/');
            } else {
                $this->phalconViewPath =
                    base_path('system/Base/Providers/ErrorServiceProvider/View/');
            }
        }
    }

    public function setPhalconViewLayoutPath($path = null)
    {
        if ($path) {
            $this->phalconViewLayoutPath = $path;
            return;
        }

        if (!isset($this->phalconViewLayoutPath)) {
            if ($this->app && $this->view) {
                $this->phalconViewLayoutPath =
                    base_path('apps/' .
                              ucfirst($this->app['app_type']) .
                              '/Views/' . $this->view['name'] .
                              '/html/layouts/');
            } else {
                $this->phalconViewLayoutPath =
                    base_path('system/Base/Providers/ErrorServiceProvider/View/layouts/');
            }
        }
    }

    public function setAssetsPath($path = null)
    {
        if ($path) {
            $this->assetsPath = $path;

            return;
        }

        if (!isset($this->assetsPath)) {
            if ($this->app && $this->view) {
                $this->assetsPath =
                    'public/' . strtolower($this->app['app_type']) . '/' . strtolower($this->view['name']) . '/';
            } else {
                $this->assetsPath = 'public/core/default/';
            }
        }
    }

    public function setPhalconViewLayoutFile()
    {
        if (!isset($this->phalconViewLayoutFile)) {
            if ($this->viewSettings && isset($this->viewSettings['layouts'])) {
                foreach ($this->viewSettings['layouts'] as $layout) {
                    if (isset($layout['active']) && $layout['active'] == true) {
                        $this->activeLayout = $layout['view'];
                        break;
                    }
                }
            }
        }

        if (!$this->activeLayout) {
            $this->activeLayout = 'default';
        }
        if ($this->activeLayout && !$this->phalconViewLayoutFile) {
            $this->phalconViewLayoutFile = $this->activeLayout;
        } else if (!$this->phalconViewLayoutFile) {
            $this->phalconViewLayoutFile = 'default';
        }
    }

    public function getVoltCompiledPath()
    {
        return $this->voltCompiledPath;
    }

    public function getPhalconViewPath()
    {
        return $this->phalconViewPath;
    }

    public function getPhalconViewLayoutPath()
    {
        return $this->phalconViewLayoutPath;
    }

    public function getAssetsPath()
    {
        return $this->assetsPath;
    }

    public function getPhalconViewLayoutFile()
    {
        return $this->phalconViewLayoutFile;
    }

    public function getActiveLayout()
    {
        return $this->activeLayout;
    }

    public function getCache()
    {
        return $this->cache;
    }

    public function getViewInfo()
    {
        return $this->view;
    }

    public function getViewSettings()
    {
        return $this->viewSettings;
    }

    public function getViewTags()
    {
        return $this->tags;
    }

    protected function setApp()
    {
        if (!$this->app) {
            $this->app = $this->apps->getAppInfo();

            $this->domain = $this->domains->getDomain();

            if ($this->app &&
                isset($this->domain['apps'][$this->app['id']]['view'])
            ) {
                $viewsName = $this->getViewById($this->domain['apps'][$this->app['id']]['view'])['name'];
                //Get views settings
                $viewsSettings = $this->modules->viewsSettings->getViewsSettingsByViewIdDomainIdAndAppId(
                    $this->domain['apps'][$this->app['id']]['view'],
                    $this->domain['id'],
                    $this->app['id']
                );

                if ($viewsSettings) {
                    if (is_string($viewsSettings['settings'])) {
                        $this->viewSettings = $this->helper->decode($viewsSettings['settings'], true);
                    } else {
                        $this->viewSettings = $viewsSettings['settings'];
                    }
                }
            } else {
                $viewsName =  'Default';
            }

            if (!$this->view) {
                //Make sure view has proper app ID.
                if ($this->app) {
                    $this->view = $this->getViewByNameForAppId($viewsName, $this->app['id']);
                }
            }
            if ($this->view) {
                if (!$this->viewSettings) {
                    if (is_string($this->view['settings'])) {
                        $this->viewSettings = $this->helper->decode($this->view['settings'], true);
                    } else {
                        $this->viewSettings = $this->view['settings'];
                    }
                }

                $this->cache = $this->config->cache->enabled;

                if (isset($this->viewSettings['tags']) && $this->viewSettings['tags']) {
                    $this->tags = $this->checkTagsPackage($this->viewSettings['tags']);
                }
            } else {
                $this->cache = false;
                $this->tags = false;
            }
        }
    }

    protected function checkTagsPackage($packageName)
    {
        return
            $this->modules->packages->getPackageByNameForAppId(
                $this->helper->last(explode('\\', $packageName)),
                $this->apps->getAppInfo()['id']
            );
    }

    public function getViewByNameForAppId($name, $appId)
    {
        foreach($this->views as $view) {
            $view['apps'] = $this->helper->decode($view['apps'], true);

            if ((isset($view['apps'][$appId]['enabled']) &&
                $view['apps'][$appId]['enabled'] == true) &&
                strtolower($view['name']) == strtolower($name) &&
                $view['base_view_module_id'] == '0'
            ) {
                return $view;
            }
        }

        return false;
    }

    public function getViewsForAppId($appId)
    {
        $views = [];

        foreach($this->views as $view) {
            $view['apps'] = $this->helper->decode($view['apps'], true);

            if (isset($view['apps'][$appId]['enabled']) &&
                $view['apps'][$appId]['enabled'] == 'true'
            ) {
                array_push($views, $view);
            }
        }

        return $views;
    }

    public function getViewById($id)
    {
        foreach($this->views as $view) {
            if ($view['id'] == $id) {
                return $view;
            }
        }

        return false;
    }

    public function getViewByRepo($repo)
    {
        foreach($this->views as $view) {
            if ($view['repo'] == $repo) {
                return $view;
            }
        }

        return false;
    }

    public function getViewByAppTypeAndRepoAndName($appType, $repo, $name)
    {
        foreach($this->views as $view) {
            if ($view['app_type'] === $appType &&
                $view['repo'] === $repo &&
                strtolower($name) === strtolower($view['name'])
            ) {
                return $view;
            }
        }

        return false;
    }

    public function getViewsByApiId($apiId)
    {
        $views = [];

        foreach($this->views as $view) {
            if ($view['api_id'] == $apiId) {
                array_push($views, $view);
            }
        }

        return $views;
    }

    public function getViewsByBaseViewModuleId($baseViewModuleId)
    {
        $views = [];

        foreach($this->views as $view) {
            if ($view['base_view_module_id'] == $baseViewModuleId) {
                array_push($views, $view);
            }
        }

        return $views;
    }

    public function getViewByName($name)
    {
        foreach($this->views as $view) {
            if ($view['name'] == $name) {
                return $view;
            }
        }

        return false;
    }

    public function getViewsForCategory($category)
    {
        $views = [];

        foreach($this->views as $view) {
            if ($view['category'] === $category) {
                $views[$view['id']] = $view;
            }
        }

        return $views;
    }

    public function getViewsForAppType($appType)
    {
        $views = [];

        foreach($this->views as $view) {
            if ($view['app_type'] === $appType) {
                $views[$view['id']] = $view;
            }
        }

        return $views;
    }

    public function getDefaultViewForAppType($appType)
    {
        foreach($this->views as $view) {
            if ($view['app_type'] === $appType &&
                $view['name'] === 'Default'
            ) {
                return $view;
            }
        }

        return false;
    }

    public function updateViews(array $data)
    {
        $views = $this->helper->decode($data['views'], true);

        foreach ($views as $viewId => $status) {
            $view = $this->getById($viewId);

            $view['apps'] = $this->helper->decode($view['apps'], true);

            if ($status === true) {
                $view['apps'][$data['id']]['enabled'] = true;
            } else if ($status === false) {
                $view['apps'][$data['id']]['enabled'] = false;
            }

            $view['apps'] = $this->helper->encode($view['apps']);

            if (is_string($view['settings'])) {
                $view['settings'] = $this->helper->decode($view['settings'], true);
            }

            if (isset($view['settings']['tags'])) {
                $package = $this->modules->packages->getPackageByName($view['settings']['tags']);

                if ($package) {
                    $package['apps'] = $this->helper->decode($package['apps'], true);

                    $package['apps'][$data['id']]['enabled'] = true;

                    $package['apps'] = $this->helper->encode($package['apps']);

                    $this->modules->packages->update($package);
                }
            }

            $view['settings'] = $this->helper->encode($view['settings']);

            $this->update($view);
        }

        return true;
    }

    public function getCalculatedAssetsVersion()
    {
        $viewsModulesVersion = [0,0,0,1];

        $baseViewVersion = $this->view['version'];
        $baseViewVersion = explode('.', $baseViewVersion);
        foreach ($baseViewVersion as $key => $version) {
            $viewsModulesVersion[$key] = (int) $version;
        }

        $views = $this->getViewsByBaseViewModuleId($this->view['id']);

        if (count($views) > 0) {
            foreach ($views as $view) {
                $viewVersion = explode('.', $view['version']);

                foreach ($viewVersion as $key => $version) {
                    $viewsModulesVersion[$key] = $viewsModulesVersion[$key] + (int) $version;
                }
            }

            $viewsModulesVersion[3] = count($views) + $viewsModulesVersion[3];
        }

        $this->view['view_modules_version'] = $viewsModulesVersion = implode('.', $viewsModulesVersion);

        $this->update($this->view);

        return $viewsModulesVersion;
    }

    public function buildAssets($componentName = null)
    {
        if ($this->assetsCollections) {
            return;
        }

        if ($componentName) {
            $this->componentName = $componentName;
        }

        if (!$this->assetsVersion) {
            if ($this->app['app_type'] === 'core') {
                $this->assetsVersion = $this->core->getVersion();
            } else {
                if ($this->view['view_modules_version'] &&
                    $this->view['view_modules_version'] !== '0.0.0.0'
                ) {
                    $this->assetsVersion = $this->view['view_modules_version'];
                } else {
                    $this->assetsVersion = $this->getCalculatedAssetsVersion();
                }
            }
        }

        $this->buildAssetsDoctype();
        $this->buildAssetsTitle();
        $this->buildAssetsMeta();
        $this->buildAssetsHeadCss();
        $this->buildAssetsHeadJs();
        $this->buildAssetsBranding();
        $this->buildAssetsFooter();
        $this->buildAssetsFooterJs();
    }

    protected function buildAssetsDoctype()
    {
        $this->assetsCollections['doctype'] = $this->assets->collection('doctype');

        $docType = $this->tag->newInstance('doctype');

        if (isset($this->viewSettings['head']['doctype'])) {
            $settingsDoctype = $this->viewSettings['head']['doctype'];
            $docType = $docType(constant("Phalcon\Html\Helper\Doctype::$settingsDoctype"));
        } else {
            $docType = $docType(Doctype::HTML5);
        }

        $this->assetsCollections['doctype']->addInline(new Inline('doctype', $docType->__toString()));
    }

    protected function buildAssetsTitle()
    {
        $this->assetsCollections['title'] = $this->assets->collection('title');

        $title = $this->tag->newInstance('title');

        if (isset($this->viewSettings['head']['title'])) {
            $title->set($this->viewSettings['head']['title'] . ' - ' . ucfirst($this->app['name']));
        } else {
            $title->set(ucfirst($this->app['name']));
        }

        if (isset($this->componentName)) {
            $title->append(' - ' . $this->componentName);
        }

        $this->assetsCollections['title']->addInline(new Inline('title', $title->__toString()));
    }

    protected function buildAssetsMeta()
    {
        $this->assetsCollections['meta'] = $this->assets->collection('meta');

        if (isset($this->viewSettings['head']['meta']['charset'])) {
            $charset = $this->viewSettings['head']['meta']['charset'];
        } else {
            $charset = 'UTF-8';
        }

        $this->assetsCollections['meta']->addInline(new Inline('charset', $charset));

        $this->assetsCollections['meta']->addInline(
            new Inline('description', $this->viewSettings['head']['meta']['description'])
        );
        $this->assetsCollections['meta']->addInline(
            new Inline('keywords', $this->viewSettings['head']['meta']['keywords'])
        );
        $this->assetsCollections['meta']->addInline(
            new Inline('author', $this->viewSettings['head']['meta']['author'])
        );
        $this->assetsCollections['meta']->addInline(
            new Inline('viewport', $this->viewSettings['head']['meta']['viewport'])
        );
    }

    protected function buildAssetsHeadCss()
    {
        $this->assetsCollections['headLinks'] = $this->assets->collection('headLinks');

        $links = $this->viewSettings['head']['link']['href'];

        if ($this->config->dev && isset($links['assets']['dev'])) {
            $links = $links['assets']['dev'];
        } else if (isset($links['assets']['prod'])) {
            $links = $links['assets']['prod'];
        } else if (isset($links['assets']['dev'])) {
            $links = $links['assets']['dev'];//Fallback to dev if set.
        }

        if (count($links) > 0) {
            foreach ($links as $link) {
                if (!isset($link['local'])) {//Default is local script
                    $link['local'] = true;
                }

                if (!isset($link['route'])) {
                    $link['route'] = '/';
                }

                if ($link['route'] === '/' || $link['route'] === $this->extractRoute()) {
                    if ($this->config->dev) {
                        $this->assetsCollections['headLinks']->addCss($link['asset']);
                    } else {
                        $this->assetsCollections['headLinks']->addCss($link['asset'], $link['local'], false, [], $this->assetsVersion);
                    }
                }
            }
        }
    }

    protected function buildAssetsHeadJs()
    {
        $this->assetsCollections['headJs'] = $this->assets->collection('headJs');

        $scripts = $this->viewSettings['head']['script']['src'];

        if ($this->config->dev && isset($scripts['assets']['dev'])) {
            $scripts = $scripts['assets']['dev'];
        } else if (isset($scripts['assets']['prod'])) {
            $scripts = $scripts['assets']['prod'];
        } else if (isset($scripts['assets']['dev'])) {
            $scripts = $scripts['assets']['dev'];//Fallback to dev if set.
        }

        if (count($scripts) > 0) {
            foreach ($scripts as $script) {
                if (!isset($script['local'])) {//Default is local script
                    $script['local'] = true;
                }

                if (!isset($script['route'])) {
                    $script['route'] = '/';
                }

                if ($script['route'] === '/' || $script['route'] === $this->extractRoute()) {
                    if ($this->config->dev) {
                        $this->assetsCollections['headJs']->addJs($script['asset']);
                    } else {
                        $this->assetsCollections['headJs']->addJs($script['asset'], $script['local'], true, [], $this->assetsVersion);
                    }
                }
            }
        }
    }

    protected function buildAssetsBranding()
    {
        $this->assetsCollections['branding'] = $this->assets->collection('branding');

        if (is_array($this->viewSettings['branding']) && count($this->viewSettings['branding']) > 0) {
            foreach ($this->viewSettings['branding'] as $key => $brand) {
                if (isset($brand['brand'])) {
                    $this->assetsCollections['branding']->addInline(new Inline($key . '-brand', $brand['brand']));

                    if (!isset($brand['maxWidth']) && !isset($brand['maxHeight'])) {
                        $brand['maxWidth'] = 200;
                        $brand['maxHeight'] = 50;
                    }
                    $this->assetsCollections['branding']->addInline(new Inline($key . '-brand-maxWidth', $brand['maxWidth']));
                    $this->assetsCollections['branding']->addInline(new Inline($key . '-brand-maxHeight', $brand['maxHeight']));
                }
            }
        }
    }

    protected function buildAssetsFooter()
    {
        $this->assetsCollections['footer'] = $this->assets->collection('footer');

        $this->assetsCollections['footer']->addInline(new Inline('footerCopyrightfromYear', $this->viewSettings['footer']['copyright']['fromYear']));
        $this->assetsCollections['footer']->addInline(new Inline('footerCopyrightSite', $this->viewSettings['footer']['copyright']['site']));
        $this->assetsCollections['footer']->addInline(new Inline('footerCopyrightName', $this->viewSettings['footer']['copyright']['name']));
    }

    protected function buildAssetsFooterJs()
    {
        $this->assetsCollections['footerJs'] = $this->assets->collection('footerJs');

        $scripts = $this->viewSettings['footer']['script']['src'];

        if ($this->config->dev && isset($scripts['assets']['dev'])) {
            $scripts = $scripts['assets']['dev'];
        } else if (isset($scripts['assets']['prod'])) {
            $scripts = $scripts['assets']['prod'];
        } else if (isset($scripts['assets']['dev'])) {
            $scripts = $scripts['assets']['dev'];//Fallback to dev if set.
        }

        if (count($scripts) > 0) {
            foreach ($scripts as $script) {
                if (!isset($script['local'])) {//Default is local script
                    $script['local'] = true;
                }

                if (!isset($script['route'])) {
                    $script['route'] = '/';
                }

                if ($script['route'] === '/' || $script['route'] === $this->extractRoute()) {
                    if ($this->config->dev) {
                        $this->assetsCollections['footerJs']->addJs($script['asset']);
                    } else {
                        $this->assetsCollections['footerJs']->addJs($script['asset'], $script['local'], true, [], $this->assetsVersion);
                    }
                }
            }
        }
    }

    protected function extractRoute()
    {
        $route = $this->router->getMatchedRoute()->getPattern();

        if ($this->domain['exclusive_to_default_app'] == 1 &&
            $this->domain['default_app_id'] == $this->app['id']
        ) {
            return $route;
        }

        $route = trim($route, '/');

        $route = str_replace(strtolower($this->app['name']), '', $route);

        return $route;
    }
}