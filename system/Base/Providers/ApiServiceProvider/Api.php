<?php

namespace System\Base\Providers\ApiServiceProvider;

use DateInterval;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Utils;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\AuthorizationValidators\BearerTokenValidator;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\ResourceServer;
use Phalcon\Filter\Validation\Validator\PresenceOf;
use System\Base\BasePackage;
use System\Base\Providers\ApiServiceProvider\Clients;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApi;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiUsers;
use System\Base\Providers\ApiServiceProvider\Repositories\AccessTokenRepository;
use System\Base\Providers\ApiServiceProvider\Repositories\AuthCodeRepository;
use System\Base\Providers\ApiServiceProvider\Repositories\ClientRepository;
use System\Base\Providers\ApiServiceProvider\Repositories\RefreshTokenRepository;
use System\Base\Providers\ApiServiceProvider\Repositories\ScopeRepository;
use System\Base\Providers\ApiServiceProvider\Repositories\UserRepository;
use System\Base\Providers\ApiServiceProvider\Scopes;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsCanlogin;

class Api extends BasePackage
{
    protected $modelToUse = ServiceProviderApi::class;

    protected $packageName = 'apiServices';

    public $apiServices;

    public $api;

    public $client;

    public $clientRedirectUri = false;

    public $scopes;

    public $clients;

    protected $app;

    protected $keys;

    protected $response;

    public $isApi;

    protected $clientId;

    protected $deviceId;

    public $isApiCheckVia;

    public $apiNeedsAuth;

    protected $server;

    protected $resource;

    protected $headerAttributes;

    protected $refreshTokenRepository;

    public $apiCallsLimitReached = false;

    protected $encClientId;

    protected $encDeviceId;

    protected $account = null;

    protected $authenticated = false;

    public function init(bool $resetCache = false)
    {
        if ($this->container) {
            $this->scopes = new Scopes;

            $this->clients = new Clients;

            if ($this->opCache) {
                if (!$resetCache && $this->opCache->checkCache('apiServices', 'core')) {
                    $this->apiServices = $this->opCache->getCache('apiServices', 'core');
                } else {
                    $this->getAll($resetCache);

                    $this->opCache->setCache('apiServices', $this->apiServices, 'core');
                }
            } else {
                $this->getAll($resetCache);
            }
        }

        return $this;
    }

    public function addApi(array $data)
    {
        if (!isset($data['account_id'])) {
            $data['account_id'] = 0;
        }

        $data['private_key_passphrase'] = '0';
        $data['private_key'] = '0';
        $data['private_key_location'] = '0';

        if (($data = $this->preCheck($data)) === false) {
            return false;
        }

        if ($data['grant_type'] === 'authorization_code') {
            if (isset($data['account_id'])) {
                $account = $this->basepackages->accounts->getById($data['account_id']);

                if (!$account) {
                    $this->addResponse('Account with ID not found.', 1);

                    return false;
                }
            }
        }

        if ($this->add($data)) {
            $newApi = $this->packagesData->last;

            if ($newApi && $newApi['api_type'] === 'protected_grant') {
                $newApi = $this->generatePKIKeys($newApi);

                if (!$newApi) {
                    $this->removeApi($newApi);

                    $this->addResponse('Error generating Pki keys for ' . $newApi['name'] . ' api', 1);

                    return false;
                }

                if ($newApi['grant_type'] === 'authorization_code') {
                    $newApi['client_id'] = $client['client_id'];

                    $this->update($newApi);
                }
            }

            $this->addResponse('Added ' . $newApi['name'] . ' api');

            return true;
        }

        $this->addResponse('Error adding new api.', 1);
    }

    public function updateApi(array $data)
    {
        $api = $this->getById($data['id']);

        if (!$api) {
            $this->addResponse('Api with ID not found.', 1);

            return false;
        }

        $data = array_merge($api, $data);

        if (($data = $this->preCheck($data)) === false) {
            return false;
        }

        if ($data['grant_type'] === 'authorization_code') {
            if (isset($data['account_id'])) {
                $account = $this->basepackages->accounts->getById($data['account_id']);

                if (!$account) {
                    $this->addResponse('Account with ID not found.', 1);

                    return false;
                }
            }
        }

        if ($data['api_type'] === 'protected_grant') {
            if (isset($data['regenerate_pki_keys']) && $data['regenerate_pki_keys'] == 1) {
                $data = $this->generatePKIKeys($data);
            }

            if ($data['grant_type'] === 'client_credentials' && !isset($data['refresh_token_timeout'])) {
                $data['refresh_token_timeout'] = 'P1M';
            }

            if ($data['grant_type'] === 'authorization_code') {
                $client = $this->clients->getFirst('client_id', $data['client_id']);

                if (!$client) {
                    $client = $this->clients->generateClientKeys(
                        data:
                        [
                            'api_id'        => $api['id'],
                            'client_id'     => $data['client_id'],
                            'client_secret' => $data['client_secret'],
                            'redirect_url'  => $data['redirect_url'],
                            'request_url'   => $data['request_url'],
                            'forceRevoke'   => true
                        ],
                        emailNewClientDetails: ($data['email_client_details_to_user'] == '1') ? true : false,
                        account: $account,
                        viaApi: true
                    );

                    $data['client_id'] = $client['client_id'];
                }
            }

            if (isset($data['authorization_tos_pp']) && $data['authorization_tos_pp'] !== '') {
                $data['authorization_tos_pp'] = $this->escaper->html($data['authorization_tos_pp']);
            }
        }

        if ($this->update($data)) {
            $this->addResponse('Updated ' . $data['name'] . ' api');
        } else {
            $this->addResponse('Error updating api.', 1);
        }
    }

    protected function preCheck($data)
    {
        if (($data = $this->checkTimeouts($data)) === false) {
            return false;
        }

        $nonZeroFields = ['app_id', 'domain_id', 'scope_id'];
        $forceFieldRequired = [];
        $checkFields = [];
        $checkFieldsReplace = true;

        if ($data['grant_type'] === 'authorization_code') {
            array_push($nonZeroFields, 'account_id');

            $forceFieldRequired = ['client_id', 'client_secret', 'redirect_url', 'request_url'];
            $checkFields = ['client_id', 'client_secret', 'redirect_url', 'request_url'];
            $checkFieldsReplace = false;
        }

        $this->validateDataWithMetaData(data: $data, ignoreFields: ['id'],
                                        nonZeroFields: $nonZeroFields, forceFieldRequired: $forceFieldRequired,
                                        checkFields: $checkFields, checkFieldsReplace: $checkFieldsReplace);

        return $data;
    }

    protected function checkTimeouts($data)
    {
        if (isset($data['access_token_timeout']) && $data['access_token_timeout'] !== '') {
            try {
                new \DateInterval($data['access_token_timeout']);
            } catch (\Exception $e) {
                $this->addResponse('Access token timeout error: ' . $e->getMessage(), 1);

                return false;
            }
        } else {
            $data['access_token_timeout'] = 'PT1H';
        }

        if (isset($data['refresh_token_timeout']) && $data['refresh_token_timeout'] !== '') {
            try {
                new \DateInterval($data['refresh_token_timeout']);
            } catch (\Exception $e) {
                $this->addResponse('Refresh token timeout error: ' . $e->getMessage(), 1);

                return false;
            }
        } else {
            $data['refresh_token_timeout'] = 'P1M';
        }

        return $data;
    }

    public function removeApi(array $data)
    {
        $this->validateDataWithMetaData(data: $data, checkFields: ['id']);

        $api = $this->getById($data['id']);

        if (!$api) {
            $this->addResponse('Api with ID not found.', 1);

            return false;
        }

        trace([$api]);
        if (isset($data['id'])) {
            if ($this->remove($data['id'], true, false)) {
                $this->deleteAPIKeys($data['id']);

                //Revoke all clients

                $this->addResponse('Removed api');
            } else {
                $this->addResponse('Error removing api.', 1);
            }
        } else {
            $this->addResponse('Error removing api.', 1);
        }
    }

    /**
     * Check whether the connection received is API or not.
     *
     * - For public access of api (without authentication). ***pub*** is a reserved keyword in apps
     * - Public URL: {domain}/api(if not exclusive for api)/pub/app_route(if not exclusive to app)/component/method
     * - Setting client_id with Authorization header is important for our setup as we rely on the client ID to find which API needs to be instantiated
     * - Post parameter refresh is important if you are grabbing refresh token using web browser
     */
    public function isApi()
    {
        if (isset($this->isApi)) {
            return $this->isApi;
        }

        $this->isApi = false;
        $this->isApiCheckVia = false;

        if ($this->request->getBestAccept() === 'application/json') {
            $url = $this->request->getURI();

            $urlParts = explode("/", trim($url, '/'));

            if (isset($urlParts[0]) && $urlParts[0] === 'api') {
                $this->isApi = true;
            }

            if ((isset($urlParts[0]) && $urlParts[0] === 'pub') ||
                (isset($urlParts[1]) && $urlParts[1] === 'pub')
            ) {
                $this->isApi = true;

                $this->isApiCheckVia = 'pub';
            } else if ($this->request->getHeader('Authorization') !== '') {
                $this->isApi = true;

                if (str_contains($this->request->getHeader('Authorization'), 'Bearer')) {
                    $this->isApiCheckVia = 'authorization';
                } else if (str_contains($this->request->getHeader('Authorization'), 'Basic') &&
                           $this->request->getBasicAuth()
                ) {
                    $this->isApiCheckVia = 'basic';
                }
            }
        }

        return $this->isApi;
    }

    public function getApiInfo($usingIsApiCheckVia = false, $usingDomainApp = false)
    {
        if ($usingDomainApp) {
            if (!$this->apiServices) {
                $this->init();
            }

            $enabledApis = [];

            foreach ($this->apiServices as $apiService) {
                if (($apiService['status'] == '1' || $apiService['status'] === true) &&
                    $apiService['app_id'] === $this->apps->getAppInfo()['id'] &&
                    $apiService['domain_id'] === $this->domains->domain['id']
                ) {
                    $enabledApis[$apiService['id']] = $apiService;
                }
            }

            return $enabledApis;
        }

        if ($this->api) {
            return $this->api;
        }

        if ($usingIsApiCheckVia) {
            if ($this->isApiCheckVia === 'pub' || $this->isApiCheckVia === 'basic') {//Public access API or user authentication
                if ($this->isApiCheckVia === 'basic') {
                    if (!$this->app) {
                        $this->app = $this->apps->getAppInfo();
                    }

                    $this->account = $this->basepackages->accounts->checkAccount($this->request->getBasicAuth()['username'], true);

                    if (!$this->account) {
                        return false;
                    }

                    //Check if user is allowed to login onto the app. Allowed value is 1 or 2.
                    $canLogin = $this->basepackages->accounts->canLogin($this->account['id'], $this->app['id']);

                    if ($canLogin === false ||
                        ($canLogin && is_array($canLogin) && $canLogin['allowed'] == '2')
                    ) {
                        if ($this->app['can_login_role_ids']) {
                            if (is_string($this->app['can_login_role_ids'])) {
                                $this->app['can_login_role_ids'] = $this->helper->decode($this->app['can_login_role_ids'], true);
                            }

                            if (in_array($this->account['security']['role_id'], $this->app['can_login_role_ids'])) {
                                if ($canLogin === false) {
                                    if ($this->config->databasetype === 'db') {
                                        $canloginModel = new BasepackagesUsersAccountsCanlogin;

                                        $newLogin['account_id'] = $this->account['id'];
                                        $newLogin['app_id'] = $this->app['id'];
                                        $newLogin['allowed'] = '2';

                                        $canloginModel->assign($newLogin);

                                        $canloginModel->create();
                                    } else {
                                        $canloginStore = $this->ff->store('basepackages_users_accounts_canlogin');

                                        $canloginStore->insert(
                                            [
                                                'account_id'    => $this->account['id'],
                                                'app_id'        => $this->app['id'],
                                                'allowed'       => 2
                                            ]
                                        );
                                    }
                                }
                            } else {
                                $this->logger->log->debug($this->account['email'] . ' and their role is not allowed to login to app ' . $this->app['name']);

                                return false;
                            }
                        } else {
                            $this->logger->log->debug('App\'s can_login_role_ids not set for app ' . $this->app['name']);

                            return false;
                        }
                    } else if ($canLogin && is_array($canLogin) && $canLogin['allowed'] == '0') {
                        $this->logger->log->debug($this->account['email'] . ' and their role is not allowed to login to api ' . $this->app['name']);

                        return false;
                    }

                    foreach ($this->apiServices as $apiService) {
                        if (($apiService['api_type'] === 'protected_user_credentials') &&
                            $apiService['app_id'] === $this->app['id'] &&
                            $apiService['domain_id'] === $this->domains->domain['id']
                        ) {
                            $api[0] = $apiService;

                            break;
                        }
                    }
                } else {
                    foreach ($this->apiServices as $apiService) {
                        if (($apiService['api_type'] === 'public') &&
                            $apiService['app_id'] === $this->apps->getAppInfo()['id'] &&
                            $apiService['domain_id'] === $this->domains->domain['id']
                        ) {
                            $api[0] = $apiService;

                            break;
                        }
                    }
                }

                if (isset($api[0]) && $api[0]['status'] == true) {
                    $this->caching->init('apcuCache', 7200);

                    if ($this->caching->enabled) {
                        $apcuClient = $this->caching->getCache('api-clients-' . $this->request->getClientAddress());

                        if ($apcuClient) {
                            $client = [];
                            $client[0] = $apcuClient;
                        }
                    }

                    if (!isset($client)) {
                        if ($this->config->databasetype === 'db') {
                            $client = $this->clients->getByParams(
                                [
                                    'conditions'    => 'client_id = :client_id: AND revoked = :revoked: AND api_id = :api_id:',
                                    'bind'          =>
                                        [
                                            'client_id'     => $this->request->getClientAddress(),
                                            'revoked'       => '0',
                                            'api_id'        => $api[0]['id']
                                        ]
                                ]
                            );
                        } else {
                            $client = $this->clients->getByParams(
                                [
                                    'conditions' =>
                                        [
                                            ['client_id', '=', $this->request->getClientAddress()],
                                            ['revoked', '=', false],
                                            ['api_id', '=', $api[0]['id']]
                                        ]
                                ]
                            );
                        }

                        if (isset($client[0]) && $this->caching->enabled) {
                            $client[0] = $this->caching->setCache('api-clients-' . $this->request->getClientAddress(), $client[0]);
                        }
                    }

                    if (isset($client[0])) {
                        if ($this->checkCallLimits($client[0], $api[0])) {
                            $this->client = $client[0];
                            $this->apiCallsLimitReached = true;
                        } else {
                            $this->api = $api[0];
                            $this->client = $client[0];
                        }
                    } else {
                        $newClient["api_id"] = (int) $api[0]['id'];
                        $newClient["app_id"] = (int) $this->apps->getAppInfo()['id'];
                        $newClient["domain_id"] = (int) $this->domains->domain['id'];
                        $newClient["account_id"] = 0;
                        $newClient["email"] = 'pubapi@' . $this->domains->domain['name'];
                        $newClient["client_id"] = $this->request->getClientAddress();
                        $newClient["client_secret"] = $this->random->base58(32);
                        $newClient["name"] = $this->request->getClientAddress();
                        $newClient["redirectUri"] = 'https://';
                        $newClient["last_used"] = (\Carbon\Carbon::now())->toDateTimeLocalString();
                        $newClient["revoked"] = 0;

                        $this->clients->addClient($newClient, false);

                        $this->api = $api[0];
                        $this->client = $this->clients->packagesData->last;
                    }
                }
            } else if ($this->isApiCheckVia === 'authorization') {
                $authorization = \trim((string) \preg_replace('/^\s*Bearer\s/', '', $this->request->getHeader('Authorization')));
                $authorization = explode('||', $authorization);

                if (count($authorization) === 2) {
                    $this->clientId = $this->secTools->decryptBase64($authorization[1]);
                } else if (count($authorization) === 3) {
                    $this->clientId = $this->secTools->decryptBase64($authorization[1]);
                    $this->deviceId = $this->secTools->decryptBase64($authorization[2]);
                }

                if (!$this->clientId) {
                    return false;
                }

                $this->setupApiViaClientId();
            }
        }

        return $this->api;
    }

    public function setupApiViaClientId($getEncIds = false)
    {
        if (!$this->clientId) {
            if ($this->request->get('client_id')) {
                $this->clientId = $this->request->get('client_id');
                if ($getEncIds) {
                    $this->encClientId = $this->secTools->encryptBase64($this->clientId);
                }
            } else if ($this->request->getPost()['client_id']) {
                $this->clientId = $this->request->getPost()['client_id'];
                if ($getEncIds) {
                    $this->encClientId = $this->secTools->encryptBase64($this->clientId);
                }
            }

            if ($this->request->get('device_id')) {
                $this->deviceId = $this->request->get('device_id');
                if ($getEncIds) {
                    $this->encDeviceId = $this->secTools->encryptBase64($this->deviceId);
                }
            } else if (isset($this->request->getPost()['device_id'])) {
                $this->deviceId = $this->request->getPost()['device_id'];
                if ($getEncIds) {
                    $this->encDeviceId = $this->secTools->encryptBase64($this->deviceId);
                }
            }
        }

        $client = null;

        $this->caching->init('apcuCache', 7200);

        if ($this->caching->enabled) {
            $apcuClient = $this->caching->getCache('api-clients-' . $this->request->getClientAddress());

            if ($apcuClient) {
                $client[0] = $apcuClient;
            }
        }

        if ($this->config->databasetype === 'db') {
            if ($this->deviceId) {
                $params =
                    [
                        'conditions'    => 'client_id = :client_id: AND device_id = :device_id:',
                        'bind'          =>
                            [
                                'client_id'    => $this->clientId,
                                'device_id'    => $this->deviceId
                            ]
                    ];
            } else {
                $params =
                    [
                        'conditions'    => 'client_id = :client_id:',
                        'bind'          =>
                            [
                                'client_id'    => $this->clientId
                            ]
                    ];
            }
        } else {
            if ($this->deviceId) {
                $params = [
                    'conditions' => [
                        ['client_id', '=', $this->clientId],
                        ['device_id', '=', $this->deviceId]
                    ]
                ];
            } else {
                $params = [
                    'conditions' => [
                        ['client_id', '=', $this->clientId]
                    ]
                ];

            }
        }

        $client = $this->clients->getByParams($params);

        if ($client && $client && is_array($client) && isset($client[0]['api_id'])) {
            $this->account = $this->basepackages->accounts->checkAccount($client[0]['email'], true);

            if (!$this->account) {
                return false;
            }

            if ($this->caching->enabled) {
                $client[0] = $this->caching->setCache('api-clients-' . $this->request->getClientAddress(), $client[0]);
            }

            $api = $this->getById($client[0]['api_id']);

            if ($api['status'] == true) {
                if ($this->checkCallLimits($client[0], $api)) {
                    $this->apiCallsLimitReached = true;
                    $this->client = $client[0];
                } else {
                    $this->api = $api;
                    $this->client = $client[0];
                }
            }

            return true;
        }

        return false;
    }

    public function account()
    {
        if ($this->authenticated) {
            return $this->account;
        }

        return false;
    }

    public function checkCallLimits(&$client, $api)
    {
        if ((int) $api['concurrent_calls_limit'] > 0) {
            if ((int) $client['concurrent_calls_count'] >= (int) $api['concurrent_calls_limit']) {
                $this->addResponse(
                    'Rate Limit Reached! ' .
                    'Configured Concurrent Calls Limit: ' . (int) $api['concurrent_calls_limit'] . '. ' .
                    'Concurrent Calls Count: ' . (int) $client['concurrent_calls_count'] . '.',
                    1
                );

                return true;
            } else {
                $this->clients->incrementCallCount($client, $api, ['concurrent_calls_count']);
            }
        }

        $toCheckCallCount = [];

        if ((int) $api['per_minute_calls_limit'] > 0) {
            array_push($toCheckCallCount, 'per_minute_calls_count');
        }
        if ((int) $api['per_hour_calls_limit'] > 0) {
            array_push($toCheckCallCount, 'per_hour_calls_count');
        }
        if ((int) $api['per_day_calls_limit'] > 0) {
            array_push($toCheckCallCount, 'per_day_calls_count');
        }

        $this->clients->checkCallCount($client, $toCheckCallCount);

        if (in_array('per_minute_calls_count', $toCheckCallCount)) {
            if ((int) $client['per_minute_calls_count'] >= (int) $api['per_minute_calls_limit']) {
                $this->addResponse(
                    'Rate Limit Reached! ' .
                    'Configured Per Minute Calls Limit: ' . (int) $api['per_minute_calls_limit'] . '. ' .
                    'Per Minute Calls Count: ' . (int) $client['per_minute_calls_count'] . '. ' .
                    'Please make API calls after: '. \Carbon\Carbon::now()->startOfMinute()->addMinute()->toCookieString(),
                    1
                );

                return true;
            } else {
                $this->clients->incrementCallCount($client, $api, ['per_minute_calls_count']);
            }
        }

        if (in_array('per_hour_calls_count', $toCheckCallCount)) {
            if ((int) $client['per_hour_calls_count'] >= (int) $api['per_hour_calls_limit']) {
                $this->addResponse(
                    'Rate Limit Reached! ' .
                    'Configured Per Hour Calls Limit: ' . (int) $api['per_hour_calls_limit'] . '. ' .
                    'Per Hour Calls Count: ' . (int) $client['per_hour_calls_count'] . '. ' .
                    'Please make API calls after: '. \Carbon\Carbon::now()->startOfHour()->addHour()->toCookieString(),
                    1
                );

                $this->clients->resetCallsCount(['per_minute_calls_count'], $client);

                return true;
            } else {
                $this->clients->incrementCallCount($client, $api, ['per_hour_calls_count']);
            }
        }

        if (in_array('per_day_calls_count', $toCheckCallCount)) {
            if ((int) $client['per_day_calls_count'] >= (int) $api['per_day_calls_limit']) {
                $this->addResponse(
                    'Rate Limit Reached! ' .
                    'Configured Per Day Calls Limit: ' . (int) $api['per_day_calls_limit'] . '. ' .
                    'Per Day Calls Count: ' . (int) $client['per_day_calls_count'] . '. ' .
                    'Please make API calls after: '. \Carbon\Carbon::now()->startOfDay()->addDay()->toCookieString(),
                    1
                );

                $this->clients->resetCallsCount(['per_minute_calls_count', 'per_hour_calls_count'], $client);

                return true;
            } else {
                $this->clients->incrementCallCount($client, $api, ['per_day_calls_count']);
            }
        }

        return false;
    }

    public function setupApi($refreshTokenSet = false)
    {
        if ($this->api['grant_type']) {
            if (method_exists($this, $grant = "init" . ucfirst("{$this->api['grant_type']}"))) {
                try {
                    $this->initApiServer();

                    if ($refreshTokenSet) {
                        $this->initRefresh_token();

                        return;
                    }

                    $this->{$grant}();
                } catch (\throwable $e) {
                    $this->logException($e);

                    throw new \Exception($e->getMessage());
                }
            }
        }
    }

    protected function initApiServer()
    {
        $this->keys = $this->getAPIKeys();

        $this->refreshTokenRepository = new RefreshTokenRepository();

        $this->server = new AuthorizationServer(
            new ClientRepository(),
            new AccessTokenRepository(),
            new ScopeRepository(),
            new CryptKey($this->keys['private_location'], $this->keys['pki_passphrase']),
            $this->keys['enc']
        );

        $this->api['access_token_timeout'] = $this->api['access_token_timeout'] ?? 'PT1H';//1 Hour
        $this->api['refresh_token_timeout'] = $this->api['refresh_token_timeout'] ?? 'P1M';//1 Month
        $this->api['authorization_code_timeout'] = $this->api['authorization_code_timeout'] ?? 'PT10M';//10 Minutes
    }

    //Password Grant
    protected function initPassword()
    {
        $grant = new PasswordGrant(
            new UserRepository(),
            $this->refreshTokenRepository
        );

        //Refresh tokens will expire after 1 month
        $grant->setRefreshTokenTTL(new DateInterval($this->api['refresh_token_timeout']));

        // Enable the password grant on the server with a token TTL of 1 hour
        $this->server->enableGrantType(
            $grant,
            new DateInterval($this->api['access_token_timeout'])
        );
    }

    //Client Credentials Grant
    protected function initClient_credentials()
    {
        // Enable the client credentials grant on the server token TTL of 1 hour
        $this->server->enableGrantType(
            new ClientCredentialsGrant(),
            new DateInterval($this->api['access_token_timeout'])
        );
    }

    //Authorization Code Grant
    protected function initAuthorization_code()
    {
        // authorization codes will expire after 10 minutes
        $grant = new AuthCodeGrant(
             new AuthCodeRepository(),
             $this->refreshTokenRepository,
             new \DateInterval($this->api['authorization_code_timeout'])
         );

        //Refresh tokens will expire after 1 month
        $grant->setRefreshTokenTTL(new \DateInterval($this->api['refresh_token_timeout']));

        // Enable the authentication code grant on the server token TTL of 1 hour
        $this->server->enableGrantType(
            $grant,
            new \DateInterval($this->api['access_token_timeout'])
        );
    }

    //Refresh Token Grant
    protected function initRefresh_token()
    {
        $grant = new RefreshTokenGrant($this->refreshTokenRepository);

        // Refresh tokens will expire after 1 month
        $grant->setRefreshTokenTTL(new \DateInterval($this->api['refresh_token_timeout']));

        // Enable the refresh token grant on the server token TTL of 1 hour
        $this->server->enableGrantType(
            $grant,
            new \DateInterval($this->api['access_token_timeout'])
        );
    }

    public function registerClient()
    {
        $serverResponse = new Response();

        try {
            $tokenResponse = $this->server->respondToAccessTokenRequest(ServerRequest::fromGlobals(), $serverResponse);

            $responseData = $this->helper->decode((string) $tokenResponse->getBody(), true);

            $responseData['access_token'] .= '||' . $this->encClientId;

            if ($this->encDeviceId) {
                $responseData['access_token'] .= '||' . $this->encDeviceId;
            }

            $responseData['refresh_url'] = $this->links->url('register/apiClient');

            $this->addResponse('Access token generated!', 0, $responseData);

            return true;
        } catch (OAuthServerException $exception) {
            $this->logException($exception);

            $this->addResponse($exception->getMessage(), 1, []);

            return $exception->generateHttpResponse($serverResponse);
        } catch (\Exception $exception) {
            $this->logException($exception);

            $this->addResponse($exception->getMessage(), 1, []);

            $body = $serverResponse->getBody();

            $body->write($exception->getMessage());

            return $serverResponse->withStatus(500)->withBody($body);
        }
    }

    public function authorizeClient()
    {
        $serverResponse = new Response();

        try {
            $authoRequest = $this->server->validateAuthorizationRequest(ServerRequest::fromGlobals());
            $authoRequest->setUser(new ServiceProviderApiUsers());

            $authoRequest->setAuthorizationApproved(true);

            return $this->server->completeAuthorizationRequest($authoRequest, $serverResponse);
        } catch (OAuthServerException $exception) {
            $this->logException($exception);
            trace([$exception]);
            // All instances of OAuthServerException can be formatted into a HTTP response
            return $exception->generateHttpResponse($serverResponse);
        } catch (\Exception $exception) {
            $this->logException($exception);
            trace([$exception]);

            // Unknown exception
            $body = new Stream(fopen('php://temp', 'r+'));

            $body->write($exception->getMessage());

            return $serverResponse->withStatus(500)->withBody($body);
        }
    }

    public function authCheck()
    {
        //Username/password authentication
        if ($this->api['api_type'] === 'protected_user_credentials') {
            $data['user'] = $this->request->getBasicAuth()['username'];
            $data['pass'] = $this->request->getBasicAuth()['password'];

            if (!$this->secTools->checkPassword($data['pass'], $this->account['security']['password'])) {
                $isAllowed = $this->access->ipFilter->filters->bumpFilterHitCounter(true);

                throw new \Exception('Error: Username/Password incorrect!');
            }

            $filter = null;

            $this->access->ipFilter->filters->bumpFilterHitCounter(false, null, $filter, false, true);

            $this->authenticated = true;

            return true;
        }

        //Token Authorization
        $accessTokenRepository = new AccessTokenRepository();

        $this->keys = $this->getAPIKeys();

        try {
            $this->resource = new ResourceServer(
                $accessTokenRepository,
                new CryptKey($this->keys['public_location'], $this->keys['pki_passphrase']),
                new BearerTokenValidator(
                    $accessTokenRepository
                )
            );

            $validateToken = $this->resource->validateAuthenticatedRequest(ServerRequest::fromGlobals());

            $this->headerAttributes = $validateToken->getAttributes();

            if ($accessTokenRepository->isTokenExpired($this->headerAttributes['oauth_access_token_id'])) {
                throw new \Exception('Token Expired!');
            }

            $this->authenticated = true;

            return $validateToken;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getScope()
    {
        if ($this->headerAttributes && $this->headerAttributes['oauth_scopes'][0]) {
            $scope = $this->scopes->getFirst('scope_name', $this->headerAttributes['oauth_scopes'][0], false, false, null, [], true);

            if ($scope) {
                return $scope;
            }
        }

        return $this->scopes->getById($this->api['scope_id']);
    }

    public function getAvailableAPIGrantTypes()
    {
        $passwordGrant =
            [
                'password'    =>
                    [
                        'id'            => 'password',
                        'name'          => 'Password Grant (With Refresh Token)',
                    ]
            ];

        $otherGrants =
            [
                'client_credentials'   =>
                    [
                        'id'            => 'client_credentials',
                        'name'          => 'Client Credential Grant'
                    ],
                'authorization_code'    =>
                    [
                        'id'            => 'authorization_code',
                        'name'          => 'Authorization Code Grant (With Refresh Token)',
                    ]
            ];

        if ($this->access->auth->account()['security']['role_id'] == '1') {
            return array_merge($passwordGrant, $otherGrants);
        }

        return $otherGrants;
    }

    public function getAPIAvailableScopes()
    {
        if (!$this->scopes) {
            $this->init();
        }

        return $this->scopes->init()->scopes;
    }

    public function getAPIAvailableTypes()
    {
        return
            [
                'public'   =>
                    [
                        'id'            => 'public',
                        'name'          => 'Public'
                    ],
                'protected_user_credentials'    =>
                    [
                        'id'            => 'protected_user_credentials',
                        'name'          => 'Protected (User Credentials)',
                    ],
                'protected_grant'    =>
                    [
                        'id'            => 'protected_grant',
                        'name'          => 'Protected (Grant)',
                    ]
            ];
    }

    public function getOpensslAlgorithms()
    {
        $algos = [];

        foreach (\openssl_get_md_methods() as $algo) {
            $algos[$algo]['id'] = $algo;
            $algos[$algo]['name'] = strtoupper($algo);
        }

        return $algos;
    }

    public function getOpensslKeyBits()
    {
        $bits = ['2048', '4096'];

        $keyBits = [];

        foreach ($bits as $bit) {
            $keyBits[$bit]['id'] = $bit;
            $keyBits[$bit]['name'] = $bit;
        }

        return $keyBits;
    }

    public function getAPIKeysParams($id = null)
    {
        $params = '2048|sha256|8';

        try {
            if ($id) {
                $params = $this->localContent->read('system/.api/' . $id . '/.params');
            }
        } catch (FilesystemException | UnableToReadFile | \throwable $exception) {
            //Do nothing.
        }

        $params = explode('|', $params);

        return $params;
    }

    public function generatePKIKeys($data)
    {
        if (!$this->checkAPIPath($data)) {
            $this->addResponse('Not able to create api directory, contact administrator.', 1);

            return false;
        }

        if (!extension_loaded('openssl')) {
            $this->addResponse('Extension openssl not loaded.', 1);

            return false;
        }

        try {
            $key = '';
            $privateKey = '';
            $passphrase = $this->random->base58(32);
            $encryptionKeySize = isset($data['encryption_key_size']) ? (int) $data['encryption_key_size'] : 32;
            $encryptionKey = $this->random->base58();

            $config = [
                "private_key_bits" => isset($data['pki_key_size']) ? (int) $data['pki_key_size'] : 2048,
                "digest_alg" => isset($data['pki_algorithm']) ? $data['pki_algorithm'] : 'sha256'
            ];

            $pki = openssl_pkey_new($config);
            openssl_pkey_export($pki, $privateKey, $passphrase);
            $publicKey = openssl_pkey_get_details($pki)["key"];

            $key = trim($privateKey . $publicKey);

            try {
                $this->localContent->write(
                    'system/.api/' . $data['id'] . '/.params',
                    $config['private_key_bits'] . '|' . $config['digest_alg'] . '|' . $encryptionKeySize,
                    ['visibility' => 'private']);
                $this->localContent->write('system/.api/' . $data['id'] . '/.pki', $key, ['visibility' => 'private']);
                $this->localContent->write('system/.api/' . $data['id'] . '/.private', $privateKey, ['visibility' => 'private']);
                $this->localContent->write('system/.api/' . $data['id'] . '/.public', $publicKey, ['visibility' => 'private']);
                $this->localContent->write('system/.api/' . $data['id'] . '/.enc', $this->secTools->encryptBase64($encryptionKey), ['visibility' => 'private']);
            } catch (FilesystemException | UnableToWriteFile $exception) {
                throw $exception;
            }

            $data['private_key_passphrase'] = $this->secTools->encryptBase64($passphrase);
            $data['private_key'] = '1';
            $data['private_key_location'] = base_path('system/.api/' . $data['id'] . '/.pki');

            return $data;
        } catch (\Exception $e) {
            $this->addResponse($e->getMessage(), 1, []);

            return false;
        }
    }

    protected function checkAPIPath($data)
    {
        if (!is_dir(base_path('system/.api/' . $data['id'] . '/'))) {
            if (!mkdir(base_path('system/.api/' . $data['id'] . '/'), 0777, true)) {
                return false;
            }
        }

        return true;
    }

    public function getAPIKeys()
    {
        $keys = [];

        try {
            $keys['enc'] = $this->secTools->decryptBase64($this->localContent->read('system/.api/' . $this->api['id'] . '/.enc'));
            $keys['public'] = $this->localContent->read('system/.api/' . $this->api['id'] . '/.public');
            $keys['public_location'] = base_path('system/.api/' . $this->api['id'] . '/.public');
            $keys['private'] = $this->localContent->read('system/.api/' . $this->api['id'] . '/.private');
            $keys['private_location'] = base_path('system/.api/' . $this->api['id'] . '/.private');
            $keys['pki'] = $this->localContent->read('system/.api/' . $this->api['id'] . '/.pki');
            $keys['pki_location'] = base_path('system/.api/' . $this->api['id'] . '/.pki');
            $keys['pki_passphrase'] = $this->secTools->decryptBase64($this->api['private_key_passphrase']);
        } catch (FilesystemException | UnableToReadFile $exception) {
            throw $exception;
        }

        return $keys;
    }

    protected function deleteAPIKeys($id)
    {
        try {
            $this->localContent->delete('system/.api/' . $id . '/.params');
            $this->localContent->delete('system/.api/' . $id . '/.pki');
            $this->localContent->delete('system/.api/' . $id . '/.private');
            $this->localContent->delete('system/.api/' . $id . '/.public');
            $this->localContent->delete('system/.api/' . $id . '/.enc');
            $this->localContent->deleteDirectory('system/.api/' . $id);
        } catch (FilesystemException | UnableToDeleteFile | UnableToDeleteDirectory $exception) {
            throw $exception;
        }
    }

    public function getEnabledAPIByType($type)
    {
        if ($this->config->databasetype === 'db') {
            $apis =
                $this->getByParams(
                    [
                        'conditions'    => 'grant_type = :gt: AND status = :status:',
                        'bind'          => [
                            'gt'        => $type,
                            'status'    => 1
                        ]
                    ], true
                );
        } else {
            $apis = $this->getByParams(
                [
                    'conditions' => [
                        ['grant_type', '=', $type],
                        ['status', '=', (bool) true],
                    ]
                ]
            );
        }

        if (!isset($apis) || !$apis) {
            $apis = [];
        }

        return $apis;
    }

    public function generateAPIUrl($data)
    {
        $url = false;

        $this->validateDataWithMetaData(data: $data, checkFields: ['type', 'app_id', 'domain_id']);

        $app = $this->apps->getById($data['app_id']);
        if (!$app) {
            $this->addResponse('App not found', 1);

            return false;
        }
        $domain = $this->domains->getById($data['domain_id']);
        if (!$domain) {
            $this->addResponse('Domain not found', 1);

            return false;
        }

        $url = $this->request->getScheme() . '://' . $domain['name'] . '/';
        if (isset($domain['exclusive_to_default_app']) &&
            $domain['exclusive_to_default_app'] != 1
        ) {
            $url = $url . $app['route'] . '/';
        }

        if ($data['type'] === 'request' || $data['type'] === 'authorization') {
            $this->validateDataWithMetaData(data: $data, checkFields: ['client_id', 'redirect_url', 'scope_id']);

            $scope = $this->scopes->getById($data['scope_id']);
            if (!$scope) {
                $this->addResponse('Scope not found', 1);

                return false;
            }

            $url = $url . 'register/q/';

            if ($data['type'] === 'authorization') {
                $url = $url . 'state/' . $data['state'] . '/?response_type=code&client_id=' . $data['client_id'] . '&redirect_url=' . $data['redirect_url'];
            } else {
                $url = $url . 'response_type/code/client_id/' . $data['client_id'] . '/scope/' . $scope['scope_name'];

                $url = $url . '/redirect_uri/__' . $data['redirect_url'] . '__';
            }

            $this->addResponse('Generated Url', 0, ['url' => $url]);
        } else if ($data['type'] === 'redirect') {
            $this->validateDataWithMetaData(data: $data, checkFields: ['client_id', 'app_id', 'domain_id', 'scope_id']);

            $url = $url . 'register/q/authorized/true';

            $this->addResponse('Generated Url', 0, ['url' => $url]);
        }

        return $url;
    }

    public function checkAuthorizationLinkData($getData)
    {
        $this->init();

        try {
            if (isset($getData['code']) && isset($getData['api_id']) ||
               (isset($getData['code']) && isset($getData['state']) && isset($getData['api_id']))
            ) {
                $api = $this->getById($getData['api_id']);

                if ($api) {
                    $client = $this->clients->getFirst('client_id', $api['client_id'], false, false, null, [], true);

                    if ($client) {
                        $this->client = $client;
                    }

                    $this->api = $api;
                }
            } else if (!isset($getData['state'])) {//Authorize Flow 1, state is not set
                $client = $this->clients->getFirst('client_id', $getData['client_id']);

                if (!$client || ($client && $client->revoked != 0)) {
                    throw new \Exception('Client ID is incorrect.');
                }

                $api = $this->getById($client->api_id);

                if ($api &&
                    (isset($this->getData()['new']) && $this->getData()['new'] == true) ||
                    (isset($this->getData()['refresh']) && $this->getData()['refresh'] == true)
                ) {
                    $this->api = $api;

                    return $api;
                }

                if (isset($getData['state']) && $getData['state'] !== '') {
                    $api['state'] = $getData['state'];
                } else {
                    $api['state'] = null;
                }

                if (!$api || ($api && $api['grant_type'] !== 'authorization_code')) {
                    $this->addResponse('No API associated with this client ID.', 1);

                    return false;
                }

                $scope = $this->scopes->getById($api['scope_id']);

                if ($scope && $scope['scope_name'] !== $getData['scope']) {
                    $this->addResponse('Scope is incorrect.', 1);

                    return false;
                }

                $uri = $this->request->getUri();
                preg_match('/__.*/', $uri, $redirectUrl);
                if (isset($redirectUrl) && is_array($redirectUrl) && count($redirectUrl) === 1 && $redirectUrl[0] !== '') {
                    $redirectUrl = str_replace('__', '', $redirectUrl[0]);

                    if ($redirectUrl !== $client->redirectUri) {
                        $this->addResponse('Redirect URI is incorrect.', 1);

                        return false;
                    }

                    $api['redirect_url'] = $redirectUrl;

                    try {
                        $testRedirectUrl = $this->remoteWebContent->request('GET', $redirectUrl . '/test_authorized/true', ['timeout' => 1]);
                    } catch (\throwable $e) {
                        $this->addResponse('Redirect URI is incorrect. Error: ' . $e->getMessage(), 1);

                        return false;
                    }

                    if ($testRedirectUrl->getStatusCode() !== 200) {
                        $this->addResponse('Redirect URI is incorrect. Error Code: ' . $testRedirectUrl->getStatusCode(), 1);

                        return false;
                    }
                } else {
                    $this->addResponse('Redirect URI is incorrect.', 1);

                    return false;
                }

                $api['state'] = $this->secTools->random->base58(32);

                $this->update($api);

                $api['type'] = 'authorization';

                $api['authorization_url'] = $this->generateAPIUrl($api);

                $this->api = $api;
            } else {
                $api = $this->getFirst('state', $getData['state'], false, false, null, [], true);

                if ($getData['state'] !== $api['state']) {
                    $this->addResponse('State mismatch. Restart authorization process!', 1);

                    return false;
                }

                $this->client = $this->clients->getFirst('client_id', $api['client_id'], false, false, null, [], true);

                if ($this->client) {
                    if (str_contains($this->client['redirectUri'], 'register/q/authorized/true')) {
                        $this->clientRedirectUri = 'local';
                    }
                }

                $this->api = $api;

                $this->setupApi();

                return $this->authorizeClient();
            }

            return $this->api;
        } catch (\Exception $e) {
            $this->addResponse('Error: ' . $e->getMessage(), 1);
        }

        return false;
    }

    public function generateOpenapiFile($data)
    {
        if (!isset($data['id'])) {
            $this->addResponse('Api ID not set', 1);

            return false;
        }

        $api = $this->getById((int) $data['id']);

        if (!$api) {
            $this->addResponse('Api with ID not found', 1);

            return false;
        }

        $version = '0.0.0';

        if ($this->apps->apps[$api['app_id']]['app_type'] === 'core') {
            $version = $this->core->core['version'];
        } else {
            try {
                if ($this->localContent->fileExists('apps/' . ucfirst($this->apps->apps[$api['app_id']]['app_type']) . '/Install/type.json')) {
                    $type = $this->helper->decode($this->localContent->read('apps/' . ucfirst($this->apps->apps[$api['app_id']]['app_type']) . '/Install/type.json'), true);

                    if (isset($type['version'])) {
                        $version = $type['version'];
                    }
                }
            } catch (\throwable | UnableToCheckExistence | UnableToReadFile | FilesystemException $e) {
                $this->logException($e);

                $this->addResponse($e->getMessage(), 1);

                return false;
            }
        }

        //Read File Content
        try {
            if ($this->localContent->fileExists('var/api/openapi/' . $api['id'] . '/openapi.json')) {
                $fileContentArr = $this->helper->decode($this->localContent->read('var/api/openapi/' . $api['id'] . '/openapi.json'), true);

                if (isset($fileContentArr['openapi']) && $fileContentArr['openapi'] === $version) {
                    $fileContent = $this->helper->encode($fileContentArr, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE);
                }
            }
        } catch (\throwable | UnableToCheckExistence | UnableToReadFile | FilesystemException $e) {
            $this->logException($e);

            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        //Extract File Content and write to openapi.json file.
        if (!isset($fileContent)) {
            try {
                $src =
                    [
                        base_path('apps/' . ucfirst($this->apps->apps[$api['app_id']]['app_type']) . '/Install/Install.php'),
                        base_path('system/Base/BaseApi.php')
                    ];

                $componentsDir = $this->basepackages->utils->scanDir('apps/' . ucfirst($this->apps->apps[$api['app_id']]['app_type']) . '/Components/');

                if (count($componentsDir['files']) > 0) {
                    foreach ($componentsDir['files'] as $files) {
                        if (str_ends_with($files, 'Api.php')) {
                            array_push($src, base_path($files));
                        }
                    }
                }

                if ($this->apps->apps[$api['app_id']]['app_type'] === 'core') {
                    $modelFiles = [];

                    $providersArr = $this->basepackages->utils->scanDir('system/Base/Providers/');

                    foreach ($providersArr['files'] as $files) {
                        if (str_contains($files, 'ApiClientServices')) {
                            continue;
                        }

                        if (str_contains($files, '/Model/')) {
                            array_push($modelFiles, base_path($files));
                        }
                    }

                    $src = array_merge($src, $modelFiles);
                }

                $result = (new \OpenApi\Builder())->setSources($src)->build();

                if ($result) {
                    $result = $this->helper->decode($result->toJson(), true);

                    $result['info']['version'] = $version;

                    if (isset($api['openapi_name']) && $api['openapi_name'] !== '') {
                        $result['info']['title'] = $api['openapi_name'];
                    }
                    if (isset($api['openapi_description']) && $api['openapi_description'] !== '') {
                        $result['info']['description'] = $api['openapi_description'];
                    }
                    if (isset($api['openapi_email']) && $api['openapi_email'] !== '') {
                        $result['info']['contact']['email'] = $api['openapi_email'];
                    }
                    if (isset($api['openapi_license_name']) && $api['openapi_license_name'] !== '') {
                        $result['info']['license']['name'] = $api['openapi_license_name'];
                    }
                    if (isset($api['openapi_license_url']) && $api['openapi_license_url'] !== '') {
                        $result['info']['license']['url'] = $api['openapi_license_url'];
                    }

                    $devDefinedServers = $result['servers'];
                    $result['servers'] = [];

                    if (isset($api['openapi_server_sandbox_url']) && $api['openapi_server_sandbox_url'] !== '') {
                        $sandbox = [];
                        $sandbox['url'] = $api['openapi_server_sandbox_url'];

                        if (isset($api['openapi_server_sandbox_description']) && $api['openapi_server_sandbox_description'] !== '') {
                            $sandbox['description'] = $api['openapi_server_sandbox_description'];
                        }

                        array_push($result['servers'], $sandbox);
                    }

                    if (isset($api['openapi_server_production_url']) && $api['openapi_server_production_url'] !== '') {
                        $production = [];
                        $production['url'] = $api['openapi_server_production_url'];

                        if (isset($api['openapi_server_production_description']) && $api['openapi_server_production_description'] !== '') {
                            $production['description'] = $api['openapi_server_production_description'];
                        }

                        array_push($result['servers'], $production);
                    }

                    if (count($result['servers']) === 0) {
                        if (isset($devDefinedServers) && count($devDefinedServers) > 0) {
                            foreach ($devDefinedServers as $server) {
                                $serverArr = [];
                                $serverArr['url'] = $server['url'];
                                $serverArr['description'] = $server['description'];

                                array_push($result['servers'], $serverArr);
                            }
                        }
                    }

                    try {
                        $fileContent = $this->helper->encode($result, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_IGNORE);

                        $this->localContent->write('var/api/openapi/' . $api['id'] . '/openapi.json', $fileContent);

                        $this->addResponse('Ok', 0, ['fileContent' => $fileContent]);
                    } catch (\throwable | UnableToWriteFile | FilesystemException $e) {
                        $this->addResponse('Unable to write Json File to folder var/api/openapi. Check permission or contact developer.', 1);

                        return false;
                    }
                }
            } catch (\throwable $e) {
                $this->addResponse($e->getMessage(), 1);

                return false;
            }
        }

        $this->addResponse('Generated openapi specs successfully.', 0, ['fileContent' => $fileContent]);

        return true;
    }
}