<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\ApiClientServicesStats;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\ApiClientServices\BasepackagesApiClientServices;

class ApiClientServices extends BasePackage
{
    protected $modelToUse = BasepackagesApiClientServices::class;

    protected $packageName = 'apiClientServices';

    public $apiClientServices;

    public $apiConfig;

    public $apiCategories = null;

    public $apiLocations = null;

    public $apiLocation = null;

    public $apiStats;

    public $httpOptions;

    public $monitorProgress;

    public function init()
    {
        $this->packageName = 'apiClientServices';

        $this->setModelToUse(BasepackagesApiClientServices::class);

        if (!$this->apiCategories) {
            $this->registerApiCategories();
        }

        if (!$this->apiLocations) {
            $this->registerApiLocations();
        }

        $this->apiStats = new ApiClientServicesStats;

        return $this;
    }

    public function setHttpOptions(array $options)
    {
        $this->httpOptions = $options;

        return $this;
    }

    public function setMonitorProgress($monitorProgressSink, $method)
    {
        $this->monitorProgress['sink'] = $monitorProgressSink;
        $this->monitorProgress['method'] = $method;

        return $this;
    }

    public function getApiById(int $id, bool $resetCache = false, bool $enableCache = true)
    {
        $this->switchApiModel();

        $api = $this->getById($id, false, false);

        if (!$api) {
            return false;
        }

        $this->switchApiModel($api);

        $apiData = $this->getById($api['api_category_id'], false, false);

        if (!$apiData) {
            return false;
        }

        unset($apiData['id']);

        if ($apiData) {
            $api = array_merge($api, $apiData);
        }

        $api = $this->decryptPassToken($api);

        return $api;
    }

    protected function registerApiCategories()
    {
        $this->apiCategories = [];

        $basepackagesApis = $this->modules->packages->getPackagesForCategory('basepackagesApis');
        $apis = $this->modules->packages->getPackagesForCategory('appsApis');

        $apis = array_merge($basepackagesApis, $apis);

        if ($apis && is_array($apis) && count($apis) > 0) {
            foreach ($apis as $api) {
                $api['class'] = explode('\\', $api['class']);

                $category = strtolower($api['class'][$this->helper->lastKey($api['class']) - 2]);

                if (!isset($this->apiCategories[$category])) {
                    $this->apiCategories[$category]  = [];
                    $this->apiCategories[$category]['id']  = $category;
                    $this->apiCategories[$category]['name']  = ucfirst($category);
                    $this->apiCategories[$category]['childs']  = [];
                }

                $provider = strtolower($api['class'][$this->helper->lastKey($api['class']) - 1]);

                if (!isset($this->apiCategories[$category]['childs'][$provider])) {
                    $this->apiCategories[$category]['childs'][$provider] = [];
                    $this->apiCategories[$category]['childs'][$provider]['id'] = $provider;
                    $this->apiCategories[$category]['childs'][$provider]['name'] = ucfirst($api['display_name']);
                }
            }
        }
    }

    protected function registerApiLocations()
    {
        $this->apiLocations =
            [
                'basepackages'    =>
                    [
                        'id'    => 'basepackages',
                        'name'  => 'Base Packages'
                    ],
            ];

        $appTypes = $this->apps->types->types;

        foreach ($appTypes as $type) {
            $this->apiLocations[$type['app_type']]['id'] = $type['app_type'];
            $this->apiLocations[$type['app_type']]['name'] = $type['name'];
        }
    }

    protected function switchApiModel($api = null)
    {
        if ($api) {
            $api['location'] = ucfirst($api['location']);
            $api['category'] = ucfirst($api['category']);
            $api['provider'] = ucfirst($api['provider']);

            if ($api['location'] === 'Basepackages') {
                $modelClass = 'System\\Base\\Providers\\BasepackagesServiceProvider\\Packages\\Model\\ApiClientServices\\Apis\\' . $api['category'] . '\\';

                $this->setModelToUse($modelClass . 'BasepackagesApiClientServicesApis' . $api['category'] . $api['provider']);
            } else {
                $modelClass = 'Apps\\' . $api['location'] . '\\Packages\\System\\ApiClientServices\\Apis\\' . $api['category'] . '\\' . $api['provider'] . '\\';

                $this->setModelToUse($modelClass . 'Model\\SystemApiApis' . $api['category'] . $api['provider']);
            }

            $this->packageName = 'apiApis' . $api['category'] . $api['provider'];
        } else {
            $this->setModelToUse($modelToUse = BasepackagesApiClientServices::class);

            $this->packageName = 'apiClientServices';
        }
    }

    /**
     * @notification(name=add)
     */
    public function addApi(array $data)
    {
        $data = $this->encryptPassToken($data);

        $data['provider'] = strtolower($data['provider']);

        $this->switchApiModel($data);

        if ($this->add($data)) {
            $data['api_category_id'] = $this->packagesData->last['id'];

            $apiId = $this->packagesData->last['id'];

            $this->switchApiModel();

            if ($this->add($data)) {

                $data['id'] = $apiId;

                $this->apiStats->initApiCallStats($data);

                $this->addResponse('Added ' . $data['name'] . ' API');
            } else {
                $this->addResponse('Error adding new API.', 1);
            }
        }
    }

    /**
     * @notification(name=update)
     */
    public function updateApi(array $data)
    {
        $data = $this->encryptPassToken($data);

        $data['provider'] = strtolower($data['provider']);

        $this->switchApiModel($data);

        $api = $this->getById($data['id'], false, false);

        $api = array_merge($api, $data);

        if ($this->update($api)) {

            $this->apiStats->initApiCallStats($api);

            $this->switchApiModel();

            $api['id'] = $api['api_category_id'];

            if ($this->update($api)) {
                $this->addResponse('Updated ' . $data['name'] . ' API');
            } else {
                $this->addResponse('Error updating API.', 1);
            }
        } else {
            $this->addResponse('Error updating API.', 1);
        }
    }

    /**
     * @notification(name=remove)
     */
    public function removeApi(array $data)
    {
        $api = $this->getById($data['id'], false, false);

        if ($api['in_use'] == '1') {
            $this->addResponse('API in use, error removing API.', 1);

            return;
        }

        $this->switchApiModel($api);

        if ($this->remove($api['api_category_id'])) {

            $this->apiStats->removeApiCallStats($api);

            $this->switchApiModel();

            if ($this->remove($data['id'])) {
                $this->addResponse('Removed API');
            } else {
                $this->addResponse('Error removing API.', 1);
            }
        } else {
            $this->addResponse('Error removing API.', 1);
        }
    }

    /**
     * @notification(name=warning)
     * @notification_allowed_methods(email, sms)
     */
    public function warningApi($messageTitle = null, $messageDetails = null, $id = null)
    {
        if (!$messageTitle) {
            $messageTitle = 'Api has warnings, contact administrator!';
        }

        $this->addToNotification('warning', $messageTitle, $messageDetails, 'api', $id);
    }

    /**
     * @notification(name=error)
     * @notification_allowed_methods(email, sms)
     */
    public function errorApi($messageTitle = null, $messageDetails = null, $id = null)
    {
        if (!$messageTitle) {
            $messageTitle = 'Api has errors, contact administrator!';
        }

        $this->addToNotification('error', $messageTitle, $messageDetails, 'api', $id);
    }

    public function useApi($data)//or ID (integer/string)
    {
        $this->apiConfig = null;

        if (is_array($data)) {
            if (isset($data['state'])) {
                if (strpos($data['_url'], 'ebay')) {
                    $this->switchApiModel(['provider' => 'ebay']);
                } else if (strpos($data['_url'], 'xero')) {
                    $this->switchApiModel(['provider' => 'xero']);
                }

                if ($this->config->databasetype === 'db') {
                    $conditions =
                        [
                            'conditions'    => 'identifier = :identifier:',
                            'bind'          =>
                                [
                                    'identifier'    => $data['state']
                                ]
                        ];
                } else {
                    $conditions =
                        [
                            'conditions'    => ['identifier', '=', $data['state']]
                        ];
                }

                $api = $this->getByParams($conditions, false, false);

                if ($api && count($api) === 1) {
                    $apiId = $api[0]['id'];

                    $this->switchApiModel();

                    if ($this->config->databasetype === 'db') {
                        $apiApi = $this->getByParams(
                            [
                                'conditions'    => 'id = :id:',
                                'bind'          =>
                                    [
                                        'id'    => $apiId
                                    ]
                            ], false, false
                        );
                    } else {
                        $apiApi = $this->getByParams(
                            [
                                'conditions'    => ['id', '=', $apiId]
                            ], false, false
                        );
                    }

                    if (count($apiApi) === 1) {
                        $this->apiConfig = $this->getApiById($apiApi[0]['id']);
                    }
                }
            } else if (isset($data['config'])) {
                if (isset($data['config']['id'])) {
                    $this->apiConfig = $this->getApiById((int) $data['config']['id']);
                } else {
                    $this->apiConfig = $data['config'];
                }
            }
        } else if (is_int($data) || is_string($data)) {
            $this->apiConfig = $this->getApiById((int) $data);
        }

        if ($this->config->debug) {
            $this->apiConfig['debug'] = true;
        }

        if ($this->apiConfig) {
            return $this->initApi();
        }

        $this->addResponse('API Id/Config missing...', 1);

        return false;
    }

    protected function initApi(array $config = null)
    {
        if (!$config) {
            $config = $this->apiConfig;
        } else {
            $config = array_merge($config, $this->apiConfig);
        }

        $apiClass = $this->getApiClass($config['category'], $config['provider']);

        if ($apiClass) {
            try {
                return (new $apiClass())->init($config, $this, $this->httpOptions, $this->monitorProgress);
            } catch (\Exception $e) {
                throw $e;
            }
        }

        return false;
    }

    public function getApiClass($category, $provider, $basepackages = true)
    {
        //Class of API defined in the package defined the location of the API and its category and provider
        //Example: WhateverLocation\\Apis\\{Category}\\{Provider}\\API_CLASS
        $basepackagesApis = $this->modules->packages->getPackagesForCategory('basepackagesApis');
        $apis = $this->modules->packages->getPackagesForCategory('appsApis');

        $apis = array_merge($basepackagesApis, $apis);

        if ($apis && is_array($apis) && count($apis) > 0) {
            foreach ($apis as $api) {
                $api['class'] = explode('\\', $api['class']);

                $apiCategory = strtolower($api['class'][$this->helper->lastKey($api['class']) - 2]);
                $apiProvider = strtolower($api['class'][$this->helper->lastKey($api['class']) - 1]);

                if (strtolower($category) === $apiCategory &&
                    strtolower($provider) === $apiProvider
                ) {
                    return implode('\\', $api['class']);
                }
            }
        }

        return false;
    }

    public function getApiByCategory($category)
    {
        $this->getAll();

        $apis = [];

        foreach ($this->apiClientServices as $api) {
            if ($api['category'] === strtolower($category)) {
                $apis[$api['id']] = $api;
            }
        }

        return $apis;
    }

    public function getApiByRepoUrl($url, $getApiClientServiceInfo = true)
    {
        $this->getAll();

        foreach ($this->apiClientServices as $api) {
            if ($api['category'] === strtolower('repos')) {
                $this->switchApiModel($api);

                $apiData = $this->getById($api['api_category_id'], false, false);

                if ($apiData && isset($apiData['repo_url']) && $apiData['repo_url'] === $url) {
                    if ($getApiClientServiceInfo) {
                        $api['api_category'] = $apiData;

                        return $api;
                    }

                    return $apiData;
                }
            }
        }

        return false;
    }

    protected function encryptPassToken(array $data)
    {
        if (isset($data['auth_type'])) {
            if ($data['auth_type'] == 'auth') {
                $data['password'] = $this->crypt->encryptBase64(trim($data['password']), $this->secTools->getSigKey());
            } else if ($data['auth_type'] == 'access_token') {
                $data['access_token'] = $this->crypt->encryptBase64(trim($data['access_token']), $this->secTools->getSigKey());
            } else if ($data['auth_type'] == 'autho') {
                $data['authorization'] = $this->crypt->encryptBase64(trim($data['authorization']), $this->secTools->getSigKey());
            }
        }

        return $data;
    }

    protected function decryptPassToken(array $data)
    {
        if (isset($data['auth_type'])) {
            if ($data['auth_type'] == 'auth' && $data['password'] !== '') {
                $data['password'] = $this->crypt->decryptBase64($data['password'], $this->secTools->getSigKey());
            } else if ($data['auth_type'] == 'access_token' && $data['access_token'] !== '') {
                $data['access_token'] = $this->crypt->decryptBase64($data['access_token'], $this->secTools->getSigKey());
            } else if ($data['auth_type'] == 'autho' && $data['authorization'] !== '') {
                $data['authorization'] = $this->crypt->decryptBase64($data['authorization'], $this->secTools->getSigKey());
            }
        }

        return $data;
    }
}