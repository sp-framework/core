<?php

namespace Apps\Dash\Packages\System\Api;

use Apps\Dash\Packages\System\Api\Model\SystemApi;
use Apps\Dash\Packages\System\Api\Model\SystemApiCalls;
use Apps\Dash\Packages\System\Api\Model\SystemApiEbay;
use Apps\Dash\Packages\System\Api\Model\SystemApiGeneric;
use Apps\Dash\Packages\System\Api\Model\SystemApiXero;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Api extends BasePackage
{
    protected $statsDirectory = 'var/api/callStats/';

    protected $modelToUse = SystemApi::class;

    protected $packageName = 'api';

    public $api;

    public $apiConfig;

    public $apiTypes = null;

    public function getApiById(int $id, bool $resetCache = false, bool $enableCache = true)
    {
        if ($id) {
            $this->init();

            $api = $this->getById($id, false, false);

            if (!$api) {
                return false;
            }

            $this->initAPIType($api);

            $apiData = $this->getById($api['api_id'], false, false);

            if (!$apiData) {
                return false;
            }

            unset($apiData['id']);

            if ($apiData) {
                $api = array_merge($api, $apiData);
            }

            return $api;
        }

        throw new \Exception('getById needs id parameter to be set.');
    }

    public function init()
    {
        $this->modelToUse = SystemApi::class;

        $this->packageName = 'api';

        if (!$this->apiTypes) {
            $this->registerApiTypes();
        }

        return $this;
    }

    protected function registerApiTypes()
    {
        try {
            $this->apiTypes = [];

            $types = $this->localContent->listContents('apps/Dash/Packages/System/Api/Apis/');

            foreach ($types as $item) {
                if ($item instanceof \League\Flysystem\FileAttributes) {
                    $path = explode('/', $item->path());

                    $fileName = Arr::last($path);

                    array_push($this->apiTypes, strtolower(explode('.php', $fileName)[0]));
                }
            }
        } catch (FilesystemException $exception) {
            throw $exception;
        }
    }

    protected function initAPIType($data)
    {
        $this->modelToUse = 'Apps\\Dash\\Packages\\System\\Api\\Model\\SystemApi' . ucfirst($data['api_type']);

        $this->packageName = 'api' . ucfirst($data['api_type']);
    }

    /**
     * @notification(name=add)
     */
    public function addApi(array $data)
    {
        $data['api_type'] = strtolower($data['api_type']);

        $data['setup'] = 1;

        $this->initAPIType($data);

        $api = $this->initApi($data);
        var_dump($api);die();
        $data = $api->add($data);

        if ($apiData['api_type'] === 'ebay' ||
            $apiData['api_type'] === 'xero'
        ) {
            if (isset($api['user_credentials_scopes']) && $api['user_credentials_scopes'] !== '') {
                $scopes = explode(',', $api['user_credentials_scopes']);
                if (count($scopes) > 0) {
                    foreach ($scopes as &$scope) {
                        $scope = trim($scope);
                    }

                    if ($api['api_type'] === 'ebay') {
                        $api['user_credentials_scopes'] = implode(',', $scopes);
                    } else if ($api['api_type'] === 'xero') {
                        $api['user_credentials_scopes'] = implode(' ', $scopes);
                    }
                } else {
                    $api['user_credentials_scopes'] = '';
                }
            } else {
                $api['user_credentials_scopes'] = '';
            }
        }

        if ($this->add($data)) {
            $data['api_id'] = $this->packagesData->last['id'];

            $this->init();

            if ($this->add($data)) {

                $this->initApiCallStats($data);

                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' API';

                return true;
            } else {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error adding new API.';
            }
        }
    }

    /**
     * @notification(name=update)
     */
    public function updateApi(array $data)
    {
        $data['api_type'] = strtolower($data['api_type']);

        $api = $this->getById($data['id'], false, false);

        $api = array_merge($api, $data);

        if ($this->update($api)) {

            $this->initApiCallStats($api);

            $api = $this->initAPIType($api);

            $api['id'] = $api['api_id'];

            if ($api['api_type'] === 'ebay' ||
                $api['api_type'] === 'xero'
            ) {
                if (!isset($api['credentials'])) { // Data is coming from EbayAPI, no need to update scopes.
                    if (isset($api['user_credentials_scopes']) && $api['user_credentials_scopes'] !== '') {
                        $scopes = explode(',', $api['user_credentials_scopes']);
                        if (count($scopes) > 0) {
                            foreach ($scopes as &$scope) {
                                $scope = trim($scope);
                            }
                            if ($api['api_type'] === 'ebay') {
                                $api['user_credentials_scopes'] = implode(',', $scopes);
                            } else if ($api['api_type'] === 'xero') {
                                $api['user_credentials_scopes'] = implode(' ', $scopes);
                            }
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

    /**
     * @notification(name=remove)
     */
    public function removeApi(array $data)
    {
        $api = $this->getById($data['id'], false, false);

        if ($api['in_use'] == '1') {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'API in use, error removing API.';

            return;
        }

        $this->initAPIType($api);

        if ($this->remove($api['api_id'])) {

            $this->removeApiCallStats($api);

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

    public function useApi(array $data)
    {
        $this->apiConfig = null;

        if (isset($data['state'])) {
            if (strpos($data['_url'], 'ebay')) {
                $this->initAPIType(['api_type' => 'ebay']);
            } else if (strpos($data['_url'], 'xero')) {
                $this->initAPIType(['api_type' => 'xero']);
            }

            $api = $this->getByParams(
                [
                    'conditions'    => 'identifier = :identifier:',
                    'bind'          =>
                        [
                            'identifier'    => $data['state']
                        ]
                ], false, false
            );

            if ($api && count($api) === 1) {
                $apiId = $api[0]['id'];

                $this->init();

                $apiApi = $this->getByParams(
                    [
                        'conditions'    => 'api_id = :id:',
                        'bind'          =>
                            [
                                'id'    => $apiId
                            ]
                    ], false, false
                );

                if (count($apiApi) === 1) {
                    $this->apiConfig = $this->getApiById($apiApi[0]['id']);
                }
            }
        } else if (isset($data['api_id'])) {
            $this->apiConfig = $this->getApiById($data['api_id']);
        } else if (isset($data['config'])) {
            $this->apiConfig = $data['config'];
        }

        if ($this->apiConfig) {
            $api = $this->initApi();

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

        $this->packagesData->responseMessage = 'API Id/Config missing...';

        return false;
    }

    protected function initApi($config = null)
    {
        if (!$config) {
            $config = $this->apiConfig;
        }

        try {
            $apiClass = $this->getApiClass($config['api_type']);

            return (new $apiClass($config, $this))->init();

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

        return 'Apps\\Dash\\Packages\\System\\Api\\Apis' . $apiClass;
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

    protected function initApiCallStats(array $data)
    {
        if ($this->localContent->fileExists($this->statsDirectory . $data['api_type'] . '.json')) {
            $callStats =
                Json::decode($this->localContent->read($this->statsDirectory . $data['api_type'] . '.json'), true);
        }

        if (!isset($callStats[$data['api_id']])) {
            $callStats[$data['api_id']] = [];
        }

        $this->localContent->write($this->statsDirectory . $data['api_type'] . '.json', Json::encode($callStats));
    }

    protected function removeApiCallStats(array $data)
    {
        $callStats = [];

        if ($this->localContent->fileExists($this->statsDirectory . $data['api_type'] . '.json')) {
            $callStats =
                Json::decode($this->localContent->read($this->statsDirectory . $data['api_type'] . '.json'), true);
        }

        if (isset($callStats[$data['api_id']])) {
            unset($callStats[$data['api_id']]);
        }

        $this->localContent->write($this->statsDirectory . $data['api_type'] . '.json', Json::encode($callStats));
    }

    public function getApiCallStats(array $data)
    {
        if ($this->localContent->fileExists($this->statsDirectory . $data['api_type'] . '.json')) {
            $callStats =
                Json::decode($this->localContent->read($this->statsDirectory . $data['api_type'] . '.json'), true);
        }

        if (isset($callStats[$data['api_id']])) {
            return $callStats[$data['api_id']];
        }

        return [];
    }

    public function setApiCallStats($data, array $callData)
    {
        if ($this->localContent->fileExists($this->statsDirectory . $data['api_type'] . '.json')) {
            $callStats =
                Json::decode($this->localContent->read($this->statsDirectory . $data['api_type'] . '.json'), true);
        }

        $callStats[$data['api_id']]['timestamp'] = new \DateTime('now');

        $callStats[$data['api_id']]['rateLimits'] = $callData['rateLimits'];

        $this->localContent->write($this->statsDirectory . $data['api_type'] . '.json', Json::encode($callStats));
    }

    public function updateApiCallStats($callMethod, $apiId, $callStats)
    {
        $this->modelToUse = SystemApiCalls::class;

        $data['call_method'] = $callMethod;
        $data['api_id'] = $apiId;
        $data['call_exec_time'] = $callStats['total_time'];
        $data['call_response_code'] = $callStats['http_code'];
        $data['api_id'] = $apiId;
        $data['call_stats'] = Json::encode($callStats);

        $this->add($data);
    }

    public function getApiCallMethodStat($callMethod, $apiId)
    {
        $api = new SystemApiCalls;

        $methodEntry = $api::findFirst(
            [
                'conditions' => 'call_method = :cm: AND api_id = :aid: AND call_response_code = :crc:',
                'bind'       =>
                    [
                        'cm'    => $callMethod,
                        'aid'   => $apiId,
                        'crc'   => 200
                    ],
                'order'     => 'id desc'
            ]
        );

        if ($methodEntry) {

            $methodEntry = $methodEntry->toArray();

            if ($this->apiConfig['api_type'] === 'xero') {
                return \Carbon\Carbon::parse($methodEntry['called_at'])->setTimezone('UTC')->toDateTimeString();
            }

            return $methodEntry['called_at'];
        }

        return false;
    }
}