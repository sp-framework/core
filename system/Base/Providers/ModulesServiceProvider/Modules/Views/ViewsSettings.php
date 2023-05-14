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
        //
    }

    public function updateViewsSettings($data)
    {
        //
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
}