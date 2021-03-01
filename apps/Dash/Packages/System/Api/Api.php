<?php

namespace Apps\Dash\Packages\System\Api;

use Apps\Dash\Packages\System\Api\Model\SystemApi;
use Apps\Dash\Packages\System\Api\Model\SystemApiEbay;
use Apps\Dash\Packages\System\Api\Model\SystemApiGeneric;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Api extends BasePackage
{
    protected $modelToUse = SystemApi::class;

    protected $packageName = 'api';

    public $api;

    public function getApiById(int $id, bool $resetCache = false, bool $enableCache = true)
    {
        if ($id) {
            if ($enableCache) {
                $parameters = $this->paramsWithCache($this->getIdParams($id));
            } else {
                $parameters = $this->getIdParams($id);
            }

            if (!$this->config->cache->enabled) {
                $parameters = $this->getIdParams($id);
            }

            $this->model = $this->modelToUse::find($parameters);

            $api = $this->getDbData($parameters, $enableCache);

            $api = $this->initAPIType($api);

            $this->model = $this->modelToUse::find($api['api_id']);

            $apiData = $this->getDbData($parameters, $enableCache);

            if ($apiData) {
                $api = array_merge($api, $apiData);
            }

            if ($api['api_type'] === 'ebay' &&
                isset($api['setup']) && (int) $api['setup'] < 4
            ) {
                //Check ebay app token time. usually its 7200 seconds (2 hrs) if > 2hrs then reset setup to step 2.
                if (isset($api['app_access_token_valid_until'])) {
                    $timeDiff = (int) $api['app_access_token_valid_until'] - time();

                    if ($timeDiff <= 0) {
                        $api['setup'] = 2;

                        $this->updateApi($api);
                    }
                }
            }

            return $api;
        }

        throw new \Exception('getById needs id parameter to be set.');
    }

    public function init()
    {
        $this->modelToUse = SystemApi::class;

        $this->packageName = 'api';

        return $this;
    }

    protected function initAPIType($data)
    {
        if ($data['api_type'] === 'generic') {
            $this->modelToUse = SystemApiGeneric::class;

            $this->packageName = 'apiGeneric';

            $data['setup'] = 3;

        } else if ($data['api_type'] === 'ebay') {
            $this->modelToUse = SystemApiEbay::class;

            $this->packageName = 'apiEbay';
        }

        return $data;
    }

    public function addApi(array $data)
    {
        $data['api_type'] = strtolower($data['api_type']);

        $data['setup'] = 1;

        $data = $this->initAPIType($data);

        $apiData = $data;

        if ($apiData['api_type'] === 'ebay') {
            if ($apiData['user_credentials_scopes'] !== '') {
                $scopes = explode(',', $apiData['user_credentials_scopes']);
                foreach ($scopes as &$scope) {
                    $scope = trim($scope);
                }

                $apiData['user_credentials_scopes'] = Json::encode($scopes);
            } else {
                $apiData['user_credentials_scopes'] = Json::encode([]);
            }
        }

        if ($this->add($apiData)) {
            $data['api_id'] = $this->packagesData->last['id'];

            $this->init();

            if ($this->add($data)) {

                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' API';

                return true;
            } else {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error adding new API.';
            }
        }
    }

    public function updateApi(array $data)
    {
        $data['api_type'] = strtolower($data['api_type']);

        $api = $this->getById($data['id']);

        $api = array_merge($api, $data);

        if ($this->update($api)) {
            $api = $this->initAPIType($api);

            $api['id'] = $api['api_id'];

            if ($api['api_type'] === 'ebay') {
                if (!isset($api['credentials'])) { // Data is coming from EbayAPI, no need to update scopes.
                    if ($api['user_credentials_scopes'] && $api['user_credentials_scopes'] !== '') {
                        $scopes = explode(',', $api['user_credentials_scopes']);
                        if (count($scopes) > 0) {
                            foreach ($scopes as &$scope) {
                                $scope = trim($scope);
                            }
                            $api['user_credentials_scopes'] = $scopes;
                        } else {
                            $api['user_credentials_scopes'] = '';
                        }
                    } else {
                        $api['user_credentials_scopes'] = '';
                    }
                }
            }

            if ($this->update($api)) {
                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' API';

                return true;
            }

        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating API.';
        }
    }

    public function removeApi(array $data)
    {
        $api = $this->getById($data['id']);

        if ($api['in_use'] == '1') {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'API in use, error removing API.';

            return;
        }

        $this->initAPIType($api);

        if ($this->remove($api['api_id'])) {

            $this->init();

            if ($this->remove($data['id'])) {
                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Removed API';
            } else {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error removing API.';
            }
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing API.';
        }
    }

    public function useApi(array $data)
    {
        $apiConfig = null;

        if (isset($data['state'])) {
            $this->initAPIType(['api_type' => 'ebay']);

            $ebayApi = $this->getByParams(
                [
                    'conditions'    => 'identifier = :identifier:',
                    'bind'          =>
                        [
                            'identifier'    => $data['state']
                        ]
                ]
            );

            if ($ebayApi && count($ebayApi) === 1) {
                $ebayApiId = $ebayApi[0]['id'];

                $this->init();

                $ebayApiApi = $this->getByParams(
                    [
                        'conditions'    => 'api_id = :id:',
                        'bind'          =>
                            [
                                'id'    => $ebayApiId
                            ]
                    ]
                );

                if (count($ebayApiApi) === 1) {
                    $apiConfig = $this->getApiById($ebayApiApi[0]['id']);
                }
            }

        } else if (!isset($data['api_id'])) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'API Id missing.';

        } else if (isset($data['api_id'])) {

            $apiConfig = $this->getApiById($data['api_id']);
        }

        if ($apiConfig) {
            $api = $this->initApi($apiConfig);

            if (isset($data['service'])) {
                try {
                    if (isset($data['serviceRequestParams'])) {
                        return $api->useService($data['service'], $data['serviceRequestParams']);
                    } else {
                        return $api->useService($data['service']);
                    }
                } catch (\Exception $e) {
                    throw $e;
                }
            } else {
                return $api;
            }
        }

        $this->packagesData->responseCode = 1;

        $this->packagesData->responseMessage = 'API Id Wrong.';

        return false;
    }

    protected function initApi($apiConfig)
    {
        try {
            $apiClass = 'Apps\\Dash\\Packages\\System\\Api\\Apis' . $this->getApiClass($apiConfig['api_type']);

            return (new $apiClass($apiConfig, $this))->init();

        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function getApiClass($api)
    {
        $path = explode('/', $api);

        $apiClass = '';

        if (count($path) > 1) {
            foreach ($path as $value) {
                $apiClass .= '\\' . ucfirst($value);
            }
        } else {
            $apiClass .= '\\' . ucfirst($api);
        }

        return $apiClass;
    }

    public function getApiByType($type, $inuse = null)
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
                    function($api) use ($type, $inUse) {
                        $api = $api->toArray();
                        if ($api['api_type'] === strtolower($type) &&
                            $api['in_use'] == $inUse
                        ) {
                            return $api;
                        }
                    }
                );
        } else {
            $filter =
                $this->model->filter(
                    function($api) use ($type) {
                        $api = $api->toArray();
                        if ($api['api_type'] == $type) {
                            return $api;
                        }
                    }
                );
        }

        return $filter;
    }
}