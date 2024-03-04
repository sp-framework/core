<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Api;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Api\ApiStats;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Api\BasepackagesApi;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Api\BasepackagesApiCalls;

class Api extends BasePackage
{
    protected $modelToUse = BasepackagesApi::class;

    protected $packageName = 'api';

    public $api;

    public $apiConfig;

    public $apiCategories = null;

    public $apiLocations = null;

    public $apiLocation = null;

    public $apiStats;

    public function init()
    {
        $this->modelToUse = BasepackagesApi::class;

        $this->packageName = 'api';

        if (!$this->apiCategories) {
            $this->registerApiCategories();
        }

        if (!$this->apiLocations) {
            $this->registerApiLocations();
        }

        $this->apiStats = new ApiStats;

        return $this;
    }

    public function getApiById(int $id, bool $resetCache = false, bool $enableCache = true)
    {
        if ($id) {
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

        throw new \Exception('getById needs id parameter to be set.');
    }

    protected function registerApiCategories()
    {
        try {
            $this->apiCategories = [];

            $categories =
                $this->localContent->listContents(
                    'system/Base/Providers/BasepackagesServiceProvider/Packages/Api/Apis/'
                );

            $this->registerApis($categories);

            $categories =
                $this->localContent->listContents(
                    'apps/Dash/Packages/System/Api/Apis/'
                );

            $this->registerApis($categories);

        } catch (FilesystemException $exception) {
            throw $exception;
        }
    }

    protected function registerApiLocations()
    {
        $this->apiLocations =
            [
                'system'    =>
                    [
                        'id'    => 'system',
                        'name'  => 'System'
                    ],
                'apps'    =>
                    [
                        'id'    => 'apps',
                        'name'  => 'Apps'
                    ]
            ];
    }

    protected function registerApis($categories)
    {
        foreach ($categories as $item) {
            if ($item instanceof \League\Flysystem\DirectoryAttributes) {
                $path = explode('/', $item->path());

                $folderName = $this->helper->last($path);

                if (!isset($this->apiCategories[$folderName])) {
                    $this->apiCategories[strtolower($folderName)] =
                        [
                            'id'    => strtolower($folderName),
                            'name'  => $folderName
                        ];
                }

                $category = $this->localContent->listContents($item->path());

                foreach ($category as $subCategory) {
                    if ($subCategory instanceof \League\Flysystem\DirectoryAttributes) {
                        $path = explode('/', $subCategory->path());

                        $subCategoryFolderName = $this->helper->last($path);

                        if (!isset($this->apiCategories[strtolower($folderName)]['childs'][$subCategoryFolderName])) {
                            $this->apiCategories[strtolower($folderName)]['childs'][strtolower($subCategoryFolderName)] =
                                [
                                    'id'    => strtolower($subCategoryFolderName),
                                    'name'  => $subCategoryFolderName
                                ];
                        }
                    }
                }
            }
        }
    }

    protected function switchApiModel($api = null)
    {
        if ($api) {
            if ($api['location'] === 'system') {
                $modelClass = 'System\\Base\\Providers\\BasepackagesServiceProvider\\Packages\\Model\\Api\\Apis\\' . ucfirst($api['category']) . '\\';

                $this->modelToUse = $modelClass . 'BasepackagesApiApis' . ucfirst($api['category']) . ucfirst($api['provider']);
            } else if ($api['location'] === 'apps') {
                $modelClass = 'Apps\\Core\\Packages\\System\\Api\\Apis\\' . ucfirst($api['category']) . '\\' . ucfirst($api['provider']) . '\\';

                $this->modelToUse = $modelClass . 'Model\\SystemApiApis' . ucfirst($api['category']) . ucfirst($api['provider']);
            }

            $this->packageName = 'apiApis' . ucfirst($api['category']) . ucfirst($api['provider']);
        } else {
            $this->modelToUse = $modelToUse = BasepackagesApi::class;


            $this->packageName = 'api';
        }

        $this->setFFStoreToUse();
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
                $this->apiConfig = $data['config'];
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

        try {
            $apiClass = $this->getApiClass($config['category'], $config['provider']);

            return (new $apiClass())->init($config, $this);
        } catch (\throwable $e) {
            try {
                $apiClass = $this->getApiClass($config['category'], $config['provider'], false);

                return (new $apiClass())->init($config, $this);
            } catch (\throwable $e) {
                return false;
            }

            return false;
        }
    }

    public function getApiClass($category, $provider, $basepackages = true)
    {
        if ($basepackages) {
            $this->apiLocation = 'system';

            return 'System\\Base\\Providers\\BasepackagesServiceProvider\\Packages\\Api\\Apis\\' . ucfirst($category) . '\\' . ucfirst($provider) . '\\' . ucfirst($provider);
        }

        $this->apiLocation = 'apps';

        return 'Apps\\Core\\Packages\\System\\Api\\Apis' . ucfirst($category) . '\\' . ucfirst($provider) . '\\' . ucfirst($provider);
    }

    public function getApiByCategory($category, $inuse = null)
    {
        $this->getAll();

        if (isset($inuse)) {
            if ($inuse === true) {
                $inUse = '1';
            } else {
                $inUse = '0'|null;
            }
            $filter =
                $this->model->filter(
                    function($api) use ($category, $inUse) {
                        $api = $this->getApiById($api->id);

                        if ($api['category'] === strtolower($category) &&
                            $api['in_use'] == $inUse
                        ) {
                            return $api;
                        }
                    }
                );
        } else {
            $filter =
                $this->model->filter(
                    function($api) use ($category) {
                        $api = $this->getApiById($api->id);

                        if ($api['category'] === strtolower($category)) {
                            return $api;
                        }
                    }
                );
        }

        return $filter;
    }

    public function getApiByProvider($provider, $inuse = null)
    {
        $this->getAll();

        if (isset($inuse)) {
            if ($inuse === true) {
                $inUse = '1';
            } else {
                $inUse = '0'|null;
            }
            $filter =
                $this->model->filter(
                    function($api) use ($provider, $inUse) {
                        $api = $this->getApiById($api->id);

                        if ($api['provider'] === strtolower($provider) &&
                            $api['in_use'] == $inUse
                        ) {
                            return $api;
                        }
                    }
                );
        } else {
            $filter =
                $this->model->filter(
                    function($api) use ($provider) {
                        $api = $this->getApiById($api->id);

                        if ($api['provider'] === strtolower($provider)) {
                            return $api;
                        }
                    }
                );
        }

        return $filter;
    }

    protected function encryptPassToken(array $data)
    {
        if (isset($data['auth_type'])) {
            if ($data['auth_type'] == 'auth') {
                $data['password'] = $this->crypt->encryptBase64($data['password'], $this->secTools->getSigKey());
            } else if ($data['auth_type'] == 'access_token') {
                $data['access_token'] = $this->crypt->encryptBase64($data['access_token'], $this->secTools->getSigKey());
            } else if ($data['auth_type'] == 'autho') {
                $data['authorization'] = $this->crypt->encryptBase64($data['authorization'], $this->secTools->getSigKey());
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