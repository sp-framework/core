<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesSettings;

class Settings extends BasePackage
{
    protected $modelToUse = BasepackagesSettings::class;

    protected $packageName = 'settings';

    public $settings;

    public function init()
    {
        $this->getAll();

        return $this;
    }

    public function getNamedSettings($name)
    {
        $filter =
            $this->model->filter(
                function($settings) use ($name) {
                    $settings = $settings->toArray();
                    if ($settings['package_name'] === ucfirst($name)) {
                        return $settings;
                    }
                }
            );

        if (count($filter) > 1) {
            throw new \Exception('Duplicate package name found for package ' . $name);
        } else if (count($filter) === 1) {
            return $filter[0];
        } else {
            return false;
        }
    }

    public function addSettings(array $data)
    {
        $data['settings'] = Json::encode($data['settings']);

        if ($this->add($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['package_name'] . ' settings';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new settings.';
        }
    }

    public function updateSettings(array $data)
    {
        $settings = $this->getNamedSettings($data['package_name']);

        if (!$settings) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Settings for package not found.';

            return;
        }

        $settings = array_merge($settings, $data);

        $settings['settings'] = Json::encode($settings['settings']);

        if ($this->update($settings)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['package_name'] . ' settings';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating settings.';
        }
    }
}