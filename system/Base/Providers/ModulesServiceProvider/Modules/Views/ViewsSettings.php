<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Views;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesViewsSettings;

class ViewsSettings extends BasePackage
{
    protected $modelToUse = ModulesViewsSettings::class;

    public $viewssettings;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

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

        $view = $this->modules->views->getById($data['view_id']);

        if (!$view) {
            $this->addResponse('Please provide correct view id', 1);

            return false;
        }

        $data['settings'] = $this->mergeViewsSettings($data, $view);

        if (!$this->basepackages->utils->validateJson(['json' => $data['settings']])) {
            $this->addResponse($this->basepackages->utils->packagesData->responseMessage, 1);

            return false;
        }

        $settings = $this->getById($data['id']);

        if ($settings) {
            $settings['settings'] = $data['settings'];

            if ($this->update($settings)) {
                $this->addResponse('Settings updated');

                return;
            }

            $this->addResponse('Error updating settings', 1);
        }
    }

    protected function mergeViewsSettings($data, $view)
    {
        if (is_string($view['settings'])) {
            $view['settings'] = Json::decode($view['settings'], true);
        }

        foreach ($view['settings']['branding'] as $brandingKey => $branding) {
            if (isset($data[$brandingKey])) {
                if ($data[$brandingKey] !== $view['settings']['branding'][$brandingKey]['brand']) {
                    $data[$brandingKey] = str_replace('public/' . $this->apps->getAppInfo()['app_type'] . '/' . strtolower($this->modules->views->getViewInfo()['name']) . '/images/', '', $data[$brandingKey]);

                    $view['settings']['branding'][$brandingKey]['brand'] = $data[$brandingKey];
                }
                unset($data[$brandingKey]);
            }
        }

        $view['settings']['head']['title'] = $data['head_title'];
        unset($data['head_title']);
        $view['settings']['head']['meta']['keywords'] = $data['head_meta_keywords'];
        unset($data['head_meta_keywords']);
        $view['settings']['head']['meta']['description'] = $data['head_meta_description'];
        unset($data['head_meta_description']);
        $view['settings']['footer']['copyright']['name'] = $data['copyright_name'];
        unset($data['copyright_name']);
        $view['settings']['footer']['copyright']['site'] = $data['copyright_site'];
        unset($data['copyright_site']);
        $view['settings']['footer']['copyright']['fromYear'] = $data['copyright_fromYear'];
        unset($data['copyright_fromYear']);

        $data['settings'] = $view['settings'];

        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['settings'] = Json::encode($data['settings']);

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
            $viewModule = $this->basepackages->utils->jsonDecodeData($viewModule);

            $this->addResponse('Loaded settings from modules view.', 0, ['settings' => $viewModule['settings']]);

            return true;
        }

        $this->addResponse('Settings not found', 1);

        return false;
    }
}