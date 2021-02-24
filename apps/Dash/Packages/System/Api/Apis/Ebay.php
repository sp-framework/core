<?php

namespace Apps\Dash\Packages\System\Api\Apis;

use Apps\Dash\Packages\System\Api\Apis\Ebay\OAuth\Types\GetUserTokenRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Ebay\OAuth\Types\RefreshUserTokenRestRequest;
use Apps\Dash\Packages\System\Api\Base\Functions;
use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Packages\PackagesData;

class Ebay
{
     const MIN_USER_TOKEN_TIME = 600;

     const MAX_REFRESH_TOKEN_TIME = 864000;

    public static $STRICT_PROPERTY_TYPES = true;

    private static $sandbox = true;

    private static $debug = false;

    protected $apiConfig;

    protected $api;

    public $packagesData;

    public function __construct($apiConfig, $api)
    {
        $this->apiConfig = $apiConfig;

        $this->api = $api;

        $this->mergeEbayConfigs();
    }

    public function init()
    {
        $this->packagesData = new PackagesData;

        if ($this->apiConfig['setup'] == '4') {
            $this->checkUserToken();
        }

        return $this;
    }

    public function getApiConfig()
    {
        return $this->apiConfig;
    }

    protected function mergeEbayConfigs()
    {
        $this->apiConfig['debug'] = self::$debug;

        $this->apiConfig['sandbox'] = self::$sandbox;
        if ($this->apiConfig['use_systems_credentials'] == 1) {
            try {
                $config = include(base_path('apps/Dash/Packages/System/Api/Configs/BazaariEbayConfig.php'));

                if (self::$sandbox) {
                    $this->apiConfig = Functions::arrayMergeDeep($this->apiConfig, $config['sandbox']);
                    $this->apiConfig['sandbox'] = true;
                } else {
                    $this->apiConfig = Functions::arrayMergeDeep($this->apiConfig, $config['production']);
                    $this->apiConfig['sandbox'] = false;
                }
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        } else {
            $userCredentials =
                [
                    'credentials'   =>
                    [
                        'appId'             => $this->apiConfig['user_credentials_app_id'],
                        'devId'             => $this->apiConfig['user_credentials_dev_id'],
                        'certId'            => $this->apiConfig['user_credentials_cert_id'],
                        'authToken'         => $this->apiConfig['app_access_token'],
                        'oauthUserToken'    => $this->apiConfig['user_access_token'],
                        'ruName'            => $this->apiConfig['user_credentials_ru_name'],
                        'scopes'            => Json::decode($this->apiConfig['user_credentials_scopes'], true),
                    ],
                ];

            $this->apiConfig = Functions::arrayMergeDeep($this->apiConfig, $userCredentials);
        }

        try {
            $ebayIds = include(base_path('apps/Dash/Packages/System/Api/Configs/EbayIds.php'));

            $this->apiConfig = Functions::arrayMergeDeep($this->apiConfig, $ebayIds);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function useService($serviceName, $serviceRequestParams = [])
    {
        try {
            $serviceClass =
                "Apps\\Dash\\Packages\\System\\Api\\Apis\\Ebay\\{$serviceName}\\Services\\{$serviceName}Service";

            $this->apiConfig = Functions::arrayMergeDeep($this->apiConfig, $serviceRequestParams);

            return new $serviceClass($this->apiConfig);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getAppToken()
    {
        try {
            $token = $this->useService('OAuth')->getAppToken();

            $this->updateApi($token, 'app');
        } catch (\Exception $e) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $e->getMessage();
        }
    }

    public function getUserTokenUrl(string $identifier)
    {
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

        try {
            $token = $this->useService('OAuth')->getUserToken($request);

            $this->updateApi($token, 'user');

        } catch (\Exception $e) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $e->getMessage();
        }
    }

    public function checkUserToken()
    {
        if (isset($this->apiConfig['user_access_token_valid_until'])) {

            $timeDiff = (int) $this->apiConfig['user_access_token_valid_until'] - time();

            if ($timeDiff <= self::MIN_USER_TOKEN_TIME) {
                if (isset($this->apiConfig['refresh_token'])) {
                    $refreshTokenTimeDiff = (int) $this->apiConfig['refresh_token_valid_until'] - time();

                    if ($refreshTokenTimeDiff > self::MAX_REFRESH_TOKEN_TIME) { //10 days
                        $this->refreshUserToken();
                    } else {
                        //We have to send notification to refresh token to admin.
                        $config = $this->apiConfig;

                        $config['setup'] = '5';

                        unset($config['ebayIds']);
                        unset($config['bebayConfig']);

                        $this->api->init();

                        if ($this->api->updateApi($config)) {
                            $this->packagesData->responseCode = 1;

                            $this->packagesData->responseMessage = 'Note: Token will expire in less than 10 days. Please refresh token.';
                        }
                    }
                }
            } else {
                $responseData['user_access_token_valid_until'] = date('m/d/Y H:i:s', $this->apiConfig['refresh_token_valid_until']);

                $this->packagesData->responseData = $responseData;

                $this->packagesData->responseMessage = 'Token is valid';

                $this->packagesData->responseCode = 0;
            }
        }
    }

    protected function refreshUserToken()
    {
        $request = new RefreshUserTokenRestRequest();

        $request->refresh_token = $this->apiConfig['refresh_token'];

        $request->scope = $this->apiConfig['credentials']['scopes'];

        try {
            $token = $this->useService('OAuth')->refreshUserToken($request);

            $this->updateApi($token, 'user', true);
        } catch (\Exception $e) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $e->getMessage();
        }
    }

    protected function updateApi($token, $type, $refresh = false)
    {
        if ($token->getStatusCode() !== 200) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $token->error_description;
        } else {

            $config = $this->apiConfig;

            if ($type === 'app') {

                $config['app_access_token'] = $token->access_token;

                $config['app_access_token_valid_until'] = time() + $token->expires_in;

                $config['setup'] = '3';

            } else if ($type === 'user') {
                $config['user_access_token'] = $token->access_token;

                $config['user_access_token_valid_until'] = time() + $token->expires_in;

                if (!$refresh) {
                    $config['refresh_token'] = $token->refresh_token;

                    $config['refresh_token_valid_until'] = time() + $token->refresh_token_expires_in;

                    $config['setup'] = '4';
                }
            }

            unset($config['ebayIds']);
            unset($config['bebayConfig']);

            $this->api->init();

            if ($this->api->updateApi($config)) {

                $newData = $this->api->packagesData->last;
                $responseData['user_access_token_valid_until'] = date('m/d/Y H:i:s', $newData['refresh_token_valid_until']);

                $this->packagesData->responseData = $responseData;

                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = ucfirst($type) . ' token granted and stored.';
            }
        }
    }
}