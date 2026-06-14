<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Views;

use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesViewsSettings;

class ViewsSettings extends BasePackage
{
    protected $modelToUse = ModulesViewsSettings::class;

    public $viewssettings;

    public function init(bool $resetCache = false)
    {
        if ($this->opCache) {
            if (!$resetCache && $this->opCache->checkCache('viewssettings', 'core')) {
                $this->viewssettings = $this->opCache->getCache('viewssettings', 'core');
            } else {
                $this->getAll($resetCache);

                $this->opCache->setCache('viewssettings', $this->viewssettings, 'core');
            }
        } else {
            $this->getAll($resetCache);
        }

        return $this;
    }

    public function addViewsSettings($data)
    {
        if (!isset($data['view_id'])) {
            $this->addResponse('Please provide view id', 1);

            return false;
        }

        $view = $this->modules->views->getById($data['view_id']);

        if (!$view) {
            $this->addResponse('Please provide correct view id', 1);

            return false;
        }

        if ($this->getViewsSettingsByViewIdDomainIdAndAppId($data['view_id'], $data['domain_id'], $data['app_id'])) {
            $this->addResponse('Settings already exits!', 1);

            return false;
        }

        $data['settings'] = $this->mergeViewsSettings($data, $view);

        if (!$this->basepackages->utils->validateJson(['json' => $data['settings']])) {
            $this->addResponse($this->basepackages->utils->packagesData->responseMessage, 1);

            return false;
        }

        if ($this->add($data)) {
            $this->addResponse('Settings added.');

            return;
        }

        $this->addResponse('Error adding settings', 1);
    }

    public function updateViewsSettings($data)
    {
        if (!isset($data['id']) && !isset($data['settings'])) {
            $this->addResponse('Please provide settings ID & Settings', 1);

            return false;
        }

        if (!isset($data['view_id'])) {
            $this->addResponse('Please provide view id', 1);

            return false;
        }

        if (!isset($data['domain_id'])) {
            $this->addResponse('Please provide domain id', 1);

            return false;
        }

        $view = $this->modules->views->getById($data['view_id']);

        if (!$view) {
            $this->addResponse('Please provide correct view id', 1);

            return false;
        }

        $settings = $this->getById($data['id']);

        $data['settings'] = $this->mergeViewsSettings($data, $view, $settings);

        if (!$this->basepackages->utils->validateJson(['json' => $data['settings']])) {
            $this->addResponse($this->basepackages->utils->packagesData->responseMessage, 1);

            return false;
        }

        if ($settings) {
            $settings['settings'] = $data['settings'];

            if ($this->update($settings)) {
                $this->addResponse('Settings updated');

                return;
            }
        }

        $this->addResponse('Error updating settings', 1);
    }

    protected function mergeViewsSettings($data, $view, $currentSettings = null)
    {
        if (is_string($view['settings'])) {
            $view['settings'] = $this->helper->decode($view['settings'], true);
        }

        if ((isset($data['via_app']) && $data['via_app'] === true) ||
            isset($data['via_domain']) && $data['via_domain'] === true
        ) {
            $data['settings'] = $view['settings'];

            $data['settings'] = $this->helper->encode($data['settings']);

            return $data['settings'];
        }

        if (isset($data['view_layout'])) {
            if (isset($view['settings']['layouts']) &&
                is_array($view['settings']['layouts'])
            ) {
                foreach ($view['settings']['layouts'] as &$layout) {
                    if (isset($layout['active']) && $layout['active'] == 'true') {
                        unset($layout['active']);
                    }

                    if (strtolower($data['view_layout']) === $layout['view']) {
                        $layout['active'] = true;
                    }
                }
            }

            unset($data['view_layout']);
        }

        if (isset($data['branding'])) {
            if (is_string($data['branding'])) {
                $data['branding'] = $this->helper->decode($data['branding'], true);
            }
        } else if (isset($data['settings']['branding'])) {//Coming from Devtools or if no branding is set.
            if ($currentSettings) {
                $data['branding'] = $currentSettings['settings']['branding'];
            } else {
                $data['branding'] = $data['settings']['branding'];
            }
        }

        if (isset($data['branding']) && count($data['branding']) > 0) {
            $domain = $this->domains->getDomainById($data['domain_id']);

            if ($domain && isset($domain['apps'][$data['app_id']]['publicStorage'])) {
                $view['settings']['branding'] = [];

                foreach ($data['branding'] as $brandingKey => $brandingUUID) {
                    if (is_string($brandingUUID)) {
                        $branding = $this->basepackages->storages->getFileInfo($brandingUUID);

                        if ($branding) {
                            $view['settings']['branding'][$brandingKey]['type'] = $branding['type'];
                            $view['settings']['branding'][$brandingKey]['uuid'] = $branding['uuid'];
                            $view['settings']['branding'][$brandingKey]['org_file_name'] = $branding['org_file_name'];
                            $view['settings']['branding'][$brandingKey]['brand'] =
                                'public/' . $domain['apps'][$data['app_id']]['publicStorage'] . '/images/assets/' . $data['id'] . '/' . $brandingUUID;

                            $view['settings']['branding'][$brandingKey]['maxWidth'] = $branding['width'];
                            $view['settings']['branding'][$brandingKey]['maxHeight'] = $branding['height'];
                            $view['settings']['branding'][$brandingKey]['links'] = $branding['links'];
                        }
                    }
                }
            }

            if (count($view['settings']['branding']) === 0) {
                $view['settings']['branding'] = $data['branding'];
            }
        }

        if (isset($data['head_title'])) {
            $view['settings']['head']['title'] = $data['head_title'];
            unset($data['head_title']);
        }
        if (isset($data['head_meta_keywords'])) {
            $view['settings']['head']['meta']['keywords'] = $data['head_meta_keywords'];
            unset($data['head_meta_keywords']);
        }
        if (isset($data['head_meta_description'])) {
            $view['settings']['head']['meta']['description'] = $data['head_meta_description'];
            unset($data['head_meta_description']);
        }
        if (isset($data['head_link_href_favicons'])) {
            if (is_string($data['head_link_href_favicons']) && $data['head_link_href_favicons'] !== '') {
                $data['head_link_href_favicons'] = $this->helper->decode($data['head_link_href_favicons'], true);
            } else {
                $data['head_link_href_favicons'] = $view['settings']['head']['link']['href']['favicons'];
            }
        } else if (isset($data['settings']['head']['link']['href']['favicons'])) {//Coming from Devtools
            if ($currentSettings) {
                $data['head_link_href_favicons'] = $currentSettings['settings']['head']['link']['href']['favicons'];
            } else {
                $data['head_link_href_favicons'] = $data['settings']['head']['link']['href']['favicons'];
            }
        }
        if (isset($data['head_link_href_favicons'])) {
            $view['settings']['head']['link']['href']['favicons'] = $data['head_link_href_favicons'];
            unset($data['head_link_href_favicons']);
        }
        if (isset($data['copyright_name'])) {
            $view['settings']['footer']['copyright']['name'] = $data['copyright_name'];
            unset($data['copyright_name']);
        }
        if (isset($data['copyright_site'])) {
            $view['settings']['footer']['copyright']['site'] = $data['copyright_site'];
            unset($data['copyright_site']);
        }
        if (isset($data['copyright_fromYear'])) {
            $view['settings']['footer']['copyright']['fromYear'] = $data['copyright_fromYear'];
            unset($data['copyright_fromYear']);
        }

        $data['settings'] = $view['settings'];

        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['settings'] = $this->helper->encode($data['settings']);

        return $data['settings'];
    }

    public function removeViewsSettings($data)
    {
        if (!isset($data['id'])) {
            $this->addResponse('Please provide settings ID', 1);

            return false;
        }

        $settings = $this->getById($data['id']);

        if ($settings) {
            if ($this->remove($data['id'])) {
                $this->addResponse('Settings removed');

                return;
            }

            $this->addResponse('Error removing settings', 1);
        }
    }

    public function getViewsSettingsById($Id)
    {
        if (!$this->viewssettings) {
            return false;
        }

        foreach($this->viewssettings as $setting) {
            if ($setting['id'] == $Id) {
                return $setting;
            }
        }

        return false;
    }

    public function getViewsSettingsByDomainId($domainId)
    {
        if (!$this->viewssettings) {
            return false;
        }

        $settings = [];

        foreach($this->viewssettings as $setting) {
            if ($setting['domain_id'] == $domainId) {
                array_push($settings, $setting);
            }
        }

        return $settings;
    }

    public function getViewsSettingsByDomainIdAndAppId($domainId, $appId)
    {
        if (!$this->viewssettings) {
            return false;
        }

        $settings = [];

        foreach($this->viewssettings as $setting) {
            if ($setting['domain_id'] == $domainId &&
                $setting['app_id'] == $appId
            ) {
                array_push($settings, $setting);
            }
        }

        return $settings;
    }

    public function getViewsSettingsByViewId($viewId)
    {
        if (!$this->viewssettings) {
            return false;
        }

        $settings = [];

        foreach($this->viewssettings as $setting) {
            if ($setting['view_id'] == $viewId) {
                $settings[$setting['id']] = $setting;
            }
        }

        if (count($settings) > 0) {
            return $settings;
        }

        return false;
    }

    public function getViewsSettingsByViewIdDomainIdAndAppId($viewId, $domainId, $appId)
    {
        if (!$this->viewssettings) {
            return false;
        }

        foreach($this->viewssettings as $setting) {
            if ($setting['view_id'] == $viewId &&
                $setting['domain_id'] == $domainId &&
                $setting['app_id'] == $appId
            ) {
                return $setting;
            }
        }

        return false;
    }

    public function getViewsSettingsFromViewModule($data)
    {
        if (!$this->viewssettings && !isset($data['view_id'])) {
            return false;
        }

        if (isset($data['viewsettings_id'])) {
            $viewSettings = $this->getViewsSettingsById($data['viewsettings_id']);

            if ($viewSettings) {
                $viewModule = $this->modules->views->getById($viewSettings['view_id']);
            }
        } else if (isset($data['view_id'])) {
            $viewModule = $this->modules->views->getById($data['view_id']);
        }

        if ($viewModule) {
            $viewModule = $this->jsonData($viewModule, true);

            $this->addResponse('Loaded settings from modules view.', 0, ['settings' => $viewModule['settings']]);

            return true;
        }

        $this->addResponse('Settings not found', 1);

        return false;
    }
}