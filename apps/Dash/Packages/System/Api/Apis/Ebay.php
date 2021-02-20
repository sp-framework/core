<?php

namespace Apps\Dash\Packages\System\Api\Apis;

use Apps\Dash\Packages\System\Api\Apis\Ebay\OAuth\Types\GetUserTokenRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Ebay\OAuth\Types\RefreshUserTokenRestRequest;
use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Packages\PackagesData;

class Ebay
{
     const MIN_USER_TOKEN_TIME = 600;

     const MAX_REFRESH_TOKEN_TIME = 864000;

    /**
     * @var bool Controls if the SDK should enforce strict types
     * when values are assigned to property classes.
     */
    public static $STRICT_PROPERTY_TYPES = true;


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

    protected function mergeEbayConfigs()
    {
        try {
            $config = include(base_path('apps/Dash/Packages/System/Api/Apis/Ebay/Configs/BazaariEbayConfig.php'));

            $this->apiConfig = drupal_array_merge_deep($this->apiConfig, $config);

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        try {
            $ebayIds = include(base_path('apps/Dash/Packages/System/Api/Apis/Ebay/Constants/EbayIds.php'));

            $this->apiConfig = drupal_array_merge_deep($this->apiConfig, $ebayIds);

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $this->apiConfig['sandbox'] = true;//Remove this
    }

    public function useService($serviceName, $serviceRequestParams = [])
    {
        try {
            $serviceClass =
                "Apps\\Dash\\Packages\\System\\Api\\Apis\\Ebay\\{$serviceName}\\Services\\{$serviceName}Service";

            $this->apiConfig = drupal_array_merge_deep($this->apiConfig, $serviceRequestParams);

            return new $serviceClass($this->apiConfig);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getAppToken()
    {
        $token = $this->useService('OAuth')->getAppToken();

        $this->updateApi($token, 'app');
    }

    public function getUserTokenUrl(string $session)
    {
        $scopes = $this->getScopes();

        $this->packagesData->responseCode = 0;

        $this->packagesData->responseMessage = 'Generated Url';

        $this->packagesData->responseData =
            $this->useService('OAuth')->redirectUrlForUser(
                [
                    'state' => $session,
                    'scope' => $scopes
                ]
            );

        $config = $this->apiConfig;

        $config['session_id'] = $session;

        $this->api->init();

        $this->api->updateApi($config);
    }

    public function addUserToken(array $data)
    {
        $request = new GetUserTokenRestRequest();

        $request->code = $data['code'];

        $token = $this->useService('OAuth')->getUserToken($request);

        $this->updateApi($token, 'user');
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
                    }
                }
            }
        }
    }

    protected function refreshUserToken()
    {
        $request = new RefreshUserTokenRestRequest();

        $request->refresh_token = $this->apiConfig['refresh_token'];

        $request->scope = $this->getScopes();

        $token = $this->useService('OAuth')->refreshUserToken($request);

        $this->updateApi($token, 'user', true);
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


    protected function getScopes()
    {
        $scopes =
            [
                'https://api.ebay.com/oauth/api_scope/sell.inventory',
                'https://api.ebay.com/oauth/api_scope/sell.account',
            ];

        if ($this->apiConfig['sandbox'] !== true) {
            $scopes = array_merge($scopes,
                [
                    'https://api.ebay.com/oauth/api_scope/commerce.identity.readonly',
                    'https://api.ebay.com/oauth/api_scope',
                    'https://api.ebay.com/oauth/api_scope/sell.marketing',
                    'https://api.ebay.com/oauth/api_scope/sell.fulfillment',
                    'https://api.ebay.com/oauth/api_scope/sell.analytics.readonly',
                    'https://api.ebay.com/oauth/api_scope/sell.finances',
                    'https://api.ebay.com/oauth/api_scope/sell.payment.dispute'
                ]
            );
        }

        return $scopes;
    }
}