<?php

namespace Apps\Dash\Packages\System\Api\Apis;

use Apps\Dash\Packages\System\Api\Apis\Xero\OAuth\Types\GetTenantsRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Xero\OAuth\Types\GetUserTokenRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Xero\OAuth\Types\RefreshUserTokenRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations\GetUsersRestRequest;
use Apps\Dash\Packages\System\Api\Base\BaseFunctions;
use Apps\Dash\Packages\System\Api\Model\SystemApi;
use Phalcon\Helper\Json;
use System\Base\Providers\ModulesServiceProvider\Modules\Packages\PackagesData;

class Simplify
{
    const MIN_USER_TOKEN_TIME = 300;

    protected static $debug = false;

    protected $apiConfig;

    protected $api;

    public $packagesData;

    public function __construct($apiConfig, $api)
    {
        $this->apiConfig = $apiConfig;

        $this->api = $api;

        $this->mergeSimplifyConfigs();
    }

    public function init()
    {
        $this->packagesData = new PackagesData;

        $this->apiConfig['debug'] = self::$debug;

        if ($this->apiConfig['setup'] == '4') {
            $this->checkUserToken();
        }

        return $this;
    }

    public function getApiConfig()
    {
        return $this->apiConfig;
    }

    protected function mergeSimplifyConfigs()
    {
        if ($this->apiConfig['use_systems_credentials'] == 1) {
            try {
                $config = include(base_path('apps/Dash/Packages/System/Api/Configs/Simplify/Config.php'));

                $this->apiConfig = BaseFunctions::arrayMergeDeep($this->apiConfig, $config['credentials']['sandbox']);

            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        } else {
            $userCredentials =
                [
                    'credentials'   =>
                    [
                        'clientId'          => $this->apiConfig['user_credentials_client_id'],
                        'clientSecret'      => $this->apiConfig['user_credentials_client_secret'],
                        'redirectUri'       => $this->apiConfig['user_credentials_redirect_uri'],
                        'authToken'         => $this->apiConfig['app_access_token'],
                        'scopes'            => $this->apiConfig['user_credentials_scopes']
                    ],
                ];

            $this->apiConfig = BaseFunctions::arrayMergeDeep($this->apiConfig, $userCredentials);
        }
    }

    public function useService($serviceName)
    {
        try {
            $serviceClass = "Apps\\Dash\\Packages\\System\\Api\\Apis\\Simplify\\Simplify_{$serviceName}";

            return $serviceClass;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getUserTokenUrl(string $identifier)
    {
        $escaper = new \Phalcon\Escaper();

        $scopes = $this->apiConfig['credentials']['scopes'];

        $this->packagesData->responseCode = 0;

        $this->packagesData->responseMessage = 'Generated Url';

        $this->packagesData->responseData =
            $this->useService('OAuth')->redirectUrlForUser(
                [
                    'state' => $identifier,
                    'scope' => $scopes
                ]
            );

        $config = $this->apiConfig;

        $config['identifier'] = $identifier;

        $this->api->init();

        $this->api->updateApi($config);
    }

    public function addUserToken(array $data)
    {
        $request = new GetUserTokenRestRequest();

        if (isset($data['error'])) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $data['error'];

            return;
        }

        $request->code = $data['code'];

        $request->state = $data['state'];

        try {
            $token = $this->useService('OAuth')->getUserToken($request);

            $this->updateApi($token, 'user');

            $this->updateApi($this->getUserTenants(), 'tenants');
        } catch (\Exception $e) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $e->getMessage();
        }
    }

    protected function getUserTenants()
    {
        $request = new GetTenantsRestRequest();

        try {
            $this->mergeSimplifyConfigs();

            $tenants = $this->useService('OAuth')->getTenants($request);

            return $tenants;
        } catch (\Exception $e) {

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $e->getMessage();
        }
    }

    public function checkUserToken()
    {
        if (isset($this->apiConfig['user_access_token_valid_until'])) {
            $responseData['user_access_token_valid_until'] =
                date('m/d/Y H:i:s', $this->apiConfig['user_access_token_valid_until']);

            $timeDiff = (int) $this->apiConfig['user_access_token_valid_until'] - time();

            if ($timeDiff <= self::MIN_USER_TOKEN_TIME) {//5mins

                if ($this->apiConfig['refresh_token'] &&
                    $this->apiConfig['refresh_token'] !== ''
                ) {
                    $this->refreshUserToken();
                } else {
                    //refreshToken missing
                    $config = $this->apiConfig;

                    $config['setup'] = '2';

                    $this->api->init();

                    if ($this->api->updateApi($config)) {
                        $this->packagesData->responseCode = 1;

                        $this->packagesData->responseMessage = 'Note: Token missing. Please refresh token.';
                    }
                }
            } else {
                $this->packagesData->responseMessage = 'Token is valid';

                $this->packagesData->responseCode = 0;
            }

            $this->packagesData->responseData = $responseData;
        }
    }

    protected function refreshUserToken()
    {
        $request = new RefreshUserTokenRestRequest();

        $request->refresh_token = $this->apiConfig['refresh_token'];

        try {
            $token = $this->useService('OAuth')->refreshUserToken($request);

            $this->updateApi($token, 'user', true);
        } catch (\Exception $e) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $e->getMessage();

            throw $e;
        }
    }

    protected function updateApi($token = null, $type, $refresh = false)
    {
        if ($token && $token->getStatusCode() !== 200) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $token->error_description;

            $this->packagesData->responseData = false;

            return;
        }

        $config = $this->apiConfig;

        if ($type === 'user') {
            $config['setup'] = '4';

            $config['user_id_token'] = $token->id_token;

            $config['user_access_token'] = $token->access_token;

            $config['user_access_token_valid_until'] = time() + $token->expires_in;

            $config['refresh_token'] = $token->refresh_token;

            $config['auth_event_id'] = $this->getAuthEventId($token->access_token);
        } else if ($type === 'tenants') {
            $tokenArr = $token->toArray();

            if (isset($tokenArr['tenants'])) {
                $config['tenants'] = Json::encode($tokenArr['tenants']);
            }
        }

        $this->api->init();

        if ($this->api->updateApi($config)) {
            $newData = $this->api->packagesData->last;
            if ($type === 'user') {
                $responseData = $newData;
                $responseData['user_access_token_valid_until'] = date('m/d/Y H:i:s', $this->apiConfig['user_access_token_valid_until']);
            } else if ($type === 'tenants') {
                $responseData['tenants'] = $newData['tenants'];
            }

            // Get updated config as original call via useService will use the old configuration.
            $this->api->init();
            $this->apiConfig = $this->api->getApiById($config['id']);

            $this->packagesData->responseData = $responseData;

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = ucfirst($type) . ' token granted and stored.';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating api after refresh_token';
        }
    }

    protected function getAuthEventId($token)
    {
        $parser = new \Phalcon\Security\JWT\Token\Parser();

        $tokenObject = $parser->parse($token);

        return $tokenObject->getClaims()->get('authentication_event_id');
    }

    public function getTenants()
    {
        $tenantsArr = Json::decode($this->apiConfig['tenants'], true);

        $responseData = [];
        $responseData['tenants'] = [];

        if (count($tenantsArr) > 0) {
            if ($this->apiConfig['tenant_id'] === '' ||
                $this->apiConfig['tenant_id'] == '0'
            ) {
                $responseData['tenants'] = $tenantsArr;
            } else {
                foreach ($tenantsArr as $key => $tenant) {
                    if ($this->apiConfig['tenant_id'] == $tenant['tenantId']) {
                        array_push($responseData['tenants'], $tenant);
                    }
                }
            }
        }

        $responseData['apiConfig'] = $this->apiConfig;

        return $responseData;
    }

    public function refreshXeroCallStats($headers = null)
    {
        $this->init();

        if (!$headers) {
            $request = new GetUsersRestRequest;

            $xeroService = $this->useService('XeroAccountingApi');

            // $xeroService->setOptionalHeader(['if-modified-since' => '1615808940294']);

            $response = $xeroService->getUsers($request);

            $headers = $response->getHeaders();
        }

        $callData = [];
        $callData['rateLimits'] = [];
        $callData['rateLimits']['appMinLimit'] = 10000;
        $callData['rateLimits']['appMinLimit-remaining'] = (int) $headers['X-AppMinLimit-Remaining'][0];
        $callData['rateLimits']['minLimit'] = 60;
        $callData['rateLimits']['minLimit-remaining'] = (int) $headers['X-MinLimit-Remaining'][0];
        $callData['rateLimits']['dayLimit'] = 5000;
        $callData['rateLimits']['dayLimit-remaining'] = (int) $headers['X-DayLimit-Remaining'][0];

        if (isset($headers['Retry-After'])) {
            $retryAfter = (int) $headers['Retry-After'][0] + time();
            $callData['rateLimits']['retry-after'] = date('m/d/Y H:i:s', $retryAfter);
        }

        $this->api->setApiCallStats($this->apiConfig, $callData);

        $getApiCallStats = $this->api->getApiCallStats($this->apiConfig);

        if ($getApiCallStats['rateLimits']['minLimit-remaining'] <= 5) {
            $this->api->warningApi(
                'Xero API Per Minute Call Limit for ' . $this->apiConfig['name'] . ' reached below 5. API call requests have been put to sleep for 5 seconds.'
            );

            sleep(5);
        }

        if ($getApiCallStats['rateLimits']['dayLimit-remaining'] <= 25) {
            $apiModel = new SystemApi;

            $api = $apiModel::findFirstById($this->apiConfig['id']);

            if ($api) {
                $this->apiConfig['in_use'] = 0;
                $this->apiConfig['used_by'] = 'ERROR: Disabled due to day limit reach. To re-enable api, please re-assign to entity again.';

                $api->assign($this->apiConfig);

                $api->update();
            }

            $this->api->errorApi(
                'Xero API Per Day Call Limit for ' .
                $this->apiConfig['name'] .
                ' reached below 25. API has been disabled for the rest of the day and need to be enabled again on limit reset.'
            );
        }

        return $getApiCallStats;
    }
}