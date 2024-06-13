<?php

namespace System\Base\Providers\ApiServiceProvider;

use DateInterval;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Utils;
use League\Flysystem\FilesystemException;
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

    protected $accessTokenRepository;

    protected $authCodeRepository;

    protected $clientRepository;

    protected $refreshTokenRepository;

    protected $scopeRepository;

    protected $userRepository;

    protected $headerAttributes;

    public $apiCallsLimitReached = false;

    public function init(bool $resetCache = false)
    {
        $this->scopes = new Scopes;

        $this->clients = new Clients;

        if ($this->container) {
            $this->getAll($resetCache);
        }

        return $this;
    }

    /**
     * @notification(name=add)
     */
    public function addApi(array $data)
    {
        $data['account_id'] = 0;

        if ($this->auth->account()) {
            $data['account_id'] = $this->auth->account()['id'];
        }

        $data['private_key_passphrase'] = '0';
        $data['private_key'] = '0';
        $data['private_key_location'] = '0';

        if (($data = $this->checkTimeouts($data)) === false) {
            return false;
        }

        if (isset($data['authorization_tos_pp']) && $data['authorization_tos_pp'] !== '') {
            $data['authorization_tos_pp'] = $this->escaper->html($data['authorization_tos_pp']);
        }

        if ($this->add($data)) {
            $newApi = $this->packagesData->last;

            if ($data['is_public'] == false) {
                $newApi = $this->generatePKIKeys($newApi);

                if ($newApi) {
                    $this->updateApi($newApi);

                    $this->addResponse('Added ' . $data['name'] . ' api');
                } else {
                    $this->removeApi($newApi);

                    return false;
                }
            }

            if ($newApi['grant_type'] === 'authorization_code') {
                if ((isset($newApi['client_id']) && $newApi['client_id'] === '') ||
                    !isset($newApi['client_id'])
                ) {
                    $client = $this->clients->generateClientKeys(
                        [
                            'api_id'        => $data['id'],
                            'client_id'     => $data['client_id'],
                            'client_secret' => $data['client_secret'],
                            'redirect_url'  => $data['redirect_url']
                        ],
                        $this->auth->account(),
                        null,
                        false
                    );

                    $newApi['client_id'] = $client['client_id'];

                    $this->updateApi($newApi);
                } else {
                    $client = $this->clients->getFirst('client_id', $newApi['client_id']);

                    if (!$client && ($newApi['client_id'] !== $data['client_id'])) {
                        $client = $this->clients->generateClientKeys(
                            [
                                'api_id'        => $data['id'],
                                'client_id'     => $data['client_id'],
                                'client_secret' => $data['client_secret'],
                                'redirect_url'  => $data['redirect_url'],
                                'forceRegen'    => true
                            ],
                            $this->auth->account(),
                            null,
                            false,
                            $data['client_id'],
                            $data['client_secret'],
                        );

                        $newApi['client_id'] = $client['client_id'];

                        $this->updateApi($newApi);
                    } else {
                        $client = $client->toArray();

                        $client['redirectUri'] = $data['redirect_url'];

                        $this->clients->updateClient($client);
                    }
                }
            }
        } else {
            $this->addResponse('Error adding new api.', 1);
        }
    }

    /**
     * @notification(name=update)
     */
    public function updateApi(array $data)
    {
        $api = $this->getById($data['id']);

        if (!$api) {
            $this->addResponse('Api with ID not found.', 1);

            return false;
        }

        if (($data = $this->checkTimeouts($data)) === false) {
            return false;
        }

        $data = array_merge($api, $data);

        if (isset($data['regenerate_pki_keys']) && $data['regenerate_pki_keys'] == 1) {
            $data = $this->generatePKIKeys($data);
        }

        if ($data['grant_type'] === 'client_credentials') {
            $data['refresh_token_timeout'] = 'P1M';
        }

        if ($data['grant_type'] === 'authorization_code') {
            if ((isset($data['client_id']) && $data['client_id'] === '') ||
                !isset($data['client_id'])
            ) {
                $client = $this->clients->generateClientKeys(
                    [
                        'api_id'        => $data['id'],
                        'client_id'     => $data['client_id'],
                        'client_secret' => $data['client_secret'],
                        'redirect_url'  => $data['redirect_url']
                    ],
                    $this->auth->account(),
                    null,
                    false
                );

                $data['client_id'] = $client['client_id'];
            } else {
                $client = $this->clients->getFirst('client_id', $data['client_id']);

                if (!$client && ($api['client_id'] !== $data['client_id'])) {
                    $client = $this->clients->generateClientKeys(
                        [
                            'api_id'        => $data['id'],
                            'client_id'     => $data['client_id'],
                            'client_secret' => $data['client_secret'],
                            'redirect_url'  => $data['redirect_url'],
                            'forceRegen'    => true
                        ],
                        $this->auth->account(),
                        null,
                        false,
                        $data['client_id'],
                        $data['client_secret']
                    );

                    $data['client_id'] = $client['client_id'];
                } else {
                    $client = $client->toArray();

                    $client['redirectUri'] = $data['redirect_url'];

                    $this->clients->updateClient($client);
                }
            }
        }

        if (isset($data['authorization_tos_pp']) && $data['authorization_tos_pp'] !== '') {
            $data['authorization_tos_pp'] = $this->escaper->html($data['authorization_tos_pp']);
        }

        if ($this->update($data)) {
            $this->addResponse('Updated ' . $data['name'] . ' api');
        } else {
            $this->addResponse('Error updating api.', 1);
        }
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

    /**
     * @notification(name=remove)
     */
    public function removeApi(array $data)
    {
        if (isset($data['id'])) {
            if ($this->remove($data['id'], true, false)) {

                $this->deleteAPIKeys($data['id']);

                $this->addResponse('Removed api');
            } else {
                $this->addResponse('Error removing api.', 1);
            }
        } else {
            $this->addResponse('Error removing api.', 1);
        }
    }

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
            //For public access of api (without authentication). pub is a reserved keyword in apps.
            //URL: {domain}/api(if not exclusive for api)/pub/app_route(if not exclusive to app)/component/method
            if ((isset($urlParts[0]) && $urlParts[0] === 'pub') ||
                (isset($urlParts[1]) && $urlParts[1] === 'pub')
            ) {
                $this->isApi = true;
                $this->isApiCheckVia = 'pub';
            } else if ($this->request->getHeader('Authorization') !== '') {//Setting client-id with Authorization header is important for our setup as we rely on the client ID to find which API needs to be instantiated
                $this->isApi = true;
                $this->isApiCheckVia = 'authorization';
            } else if ($this->request->get('client_id') &&
                       !$this->request->get('id') &&
                       !isset($this->request->getPost()['redirect_uri']) &&
                       !isset($this->request->getPost()['refresh'])//This is important if you are grabbing refresh token using web browser
            ) {
                $this->isApi = true;
                $this->isApiCheckVia = 'client_id';
                $this->clientId = $this->request->get('client_id');
                if ($this->request->get('device_id')) {
                    $this->deviceId = $this->request->get('device_id');
                }
            }
        }

        return $this->isApi;
    }

    public function getApiInfo($usingIsApiCheckVia = false, $usingDomainApp = false)
    {
        if ($this->api) {
            return $this->api;
        }

        if ($usingIsApiCheckVia) {
            if ($this->isApiCheckVia === 'pub') {//Public access API
                if ($this->config->databasetype === 'db') {
                    $params =
                        [
                            'conditions'    => 'is_public = :is_public: AND app_id = :app_id: AND domain_id = :domain_id:',
                            'bind'          =>
                                [
                                    'is_public'     => '1',
                                    'app_id'        => $this->apps->getAppInfo()['id'],
                                    'domain_id'     => $this->domains->domain['id']
                                ]
                        ];
                } else {
                    $params = [
                        'conditions' => [
                            ['is_public', '=', (bool) '1'],
                            ['app_id', '=', $this->apps->getAppInfo()['id']],
                            ['domain_id', '=', $this->domains->domain['id']]
                        ]
                    ];
                }

                $api = $this->getByParams($params);


                if ($api && isset($api[0]) && $api[0]['status'] == true) {

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

                        if ($this->caching->enabled) {
                            $client[0] = $this->caching->setCache('api-clients-' . $this->request->getClientAddress(), $client[0]);
                        }
                    }

                    if (isset($client) && $client) {
                        if ($this->checkCallLimits($client[0], $api[0])) {
                            $this->client = $client[0];
                            $this->apiCallsLimitReached = true;
                        } else {
                            $this->api = $api[0];
                            $this->client = $client[0];
                        }
                    } else {
                        $newClient["api_id"] = $api[0]['id'];
                        $newClient["app_id"] = $this->apps->getAppInfo()['id'];
                        $newClient["domain_id"] = $this->domains->domain['id'];
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
            } else if ($this->isApiCheckVia === 'authorization' ||
                       $this->isApiCheckVia === 'client_id'
            ) {
                if ($this->isApiCheckVia === 'authorization') {
                    $authorization = \trim((string) \preg_replace('/^\s*Bearer\s/', '', $this->request->getHeader('Authorization')));
                    $authorization = explode('||', $authorization);

                    if (count($authorization) === 2) {
                        $this->clientId = $this->secTools->decryptBase64($authorization[1]);
                    } else if (count($authorization) === 3) {
                        $this->clientId = $this->secTools->decryptBase64($authorization[1]);
                        $this->deviceId = $this->secTools->decryptBase64($authorization[2]);
                    }
                }

                if ($this->clientId) {
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
                    }
                }
            }
        }

        if ($usingDomainApp) {
            if ($this->config->databasetype === 'db') {
                $this->api =
                    $this->getByParams(
                        [
                            'conditions'    => 'domain_id = :did: AND app_id = :aid: AND status = :status:',
                            'bind'          => [
                                'did'       => (int) $this->domains->domain['id'],
                                'aid'       => (int) $this->apps->getAppInfo()['id'],
                                'status'    => 1
                            ]
                        ], true
                    );
            } else {
                $this->api = $this->getByParams(
                    [
                        'conditions' => [
                            ['domain_id', '=', (int) $this->domains->domain['id']],
                            ['app_id', '=', (int) $this->apps->getAppInfo()['id']],
                            ['status', '=', (bool) true],
                        ]
                    ]
                );
            }
        }

        return $this->api;
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
            if (!isset($this->{$this->api['grant_type']})) {
                if (method_exists($this, $grant = "init" . ucfirst("{$this->api['grant_type']}"))) {
                    $this->initApiServer();

                    if ($refreshTokenSet) {
                        $this->initRefresh_token();

                        return;
                    }

                    $this->{$grant}();
                }
            }
        }
    }

    protected function initApiServer()
    {
        $this->accessTokenRepository = new AccessTokenRepository();
        $this->authCodeRepository = new AuthCodeRepository();
        $this->clientRepository = new ClientRepository();
        $this->refreshTokenRepository = new RefreshTokenRepository();
        $this->scopeRepository = new ScopeRepository();
        $this->userRepository = new UserRepository();

        $this->keys = $this->getAPIKeys();

        $this->server = new AuthorizationServer(
            $this->clientRepository,
            $this->accessTokenRepository,
            $this->scopeRepository,
            new CryptKey($this->keys['private_location'], $this->keys['pki_passphrase']),
            $this->keys['enc']
        );
    }

    protected function initPassword()//Password Grant
    {
        $grant = new PasswordGrant(
            $this->userRepository,
            $this->refreshTokenRepository
        );

        $grant->setRefreshTokenTTL(new DateInterval($this->api['refresh_token_timeout'] ?? 'P1M'));// refresh tokens will expire after 1 month

        // Enable the password grant on the server with a token TTL of 1 hour
        $this->server->enableGrantType(
            $grant,
            new DateInterval($this->api['access_token_timeout'] ?? 'PT1H')// access tokens will expire after 1 hour
        );
    }

    protected function initClient_credentials()//Client Credentials Grant
    {
        // Enable the client credentials grant on the server
        $this->server->enableGrantType(
            new ClientCredentialsGrant(),
            new DateInterval($this->api['access_token_timeout'] ?? 'PT1H') // access tokens will expire after 1 hour
        );
    }

    protected function initAuthorization_code()//Authorization Code Grant
    {
        $grant = new AuthCodeGrant(
             $this->authCodeRepository,
             $this->refreshTokenRepository,
             new \DateInterval('PT10M') // authorization codes will expire after 10 minutes
         );

        $grant->setRefreshTokenTTL(new \DateInterval($this->api['refresh_token_timeout'] ?? 'P1M')); // refresh tokens will expire after 1 month

        // Enable the authentication code grant on the server
        $this->server->enableGrantType(
            $grant,
            new \DateInterval($this->api['access_token_timeout'] ?? 'PT1H') // access tokens will expire after 1 hour
        );
    }

    protected function initRefresh_token()//Refresh Token Grant
    {
        $grant = new RefreshTokenGrant($this->refreshTokenRepository);
        $grant->setRefreshTokenTTL(new \DateInterval($this->api['refresh_token_timeout'] ?? 'P1M')); // new refresh tokens will expire after 1 month

        // Enable the refresh token grant on the server
        $this->server->enableGrantType(
            $grant,
            new \DateInterval($this->api['access_token_timeout'] ?? 'PT1H') // new access tokens will expire after an hour
        );
    }

    public function registerClient()
    {
        $serverResponse = new Response();

        try {
            $tokenResponse = $this->server->respondToAccessTokenRequest(ServerRequest::fromGlobals(), $serverResponse);

            $token = $this->helper->decode((string) $tokenResponse->getBody(), true);

            if ($this->request->get('client_id')) {
                $clientId = $this->request->get('client_id');
                $encClientId = $this->secTools->encryptBase64($clientId);
            } else if ($this->request->getPost()['client_id']) {
                $clientId = $this->request->getPost()['client_id'];
                $encClientId = $this->secTools->encryptBase64($clientId);
            }

            if ($this->request->get('device_id')) {
                $deviceId = $this->request->get('device_id');
                $encDeviceId = $this->secTools->encryptBase64($deviceId);
            } else if (isset($this->request->getPost()['device_id'])) {
                $deviceId = $this->request->getPost()['device_id'];
                $encDeviceId = $this->secTools->encryptBase64($deviceId);
            }

            $token['access_token'] = $token['access_token'] . '||' . $encClientId;

            if (isset($encDeviceId)) {
                $token['access_token'] = $token['access_token'] . '||' . $encDeviceId;
            }

            if ($this->request->getPost()['grant_type'] === 'authorization_code' || $this->request->get('refresh_token')) {
                $this->addResponse('Access token generated!', 0, $token);

                return true;
            }

            $body = Utils::streamFor($this->helper->encode($token));

            return $tokenResponse->withBody($body);
        } catch (OAuthServerException $exception) {
            $this->logger->logExceptions->critical(json_trace($exception));
            $this->addResponse($exception->getMessage(), 1, []);
            var_dump($exception);die();
            // All instances of OAuthServerException can be converted to a PSR-7 response
            return $exception->generateHttpResponse($serverResponse);
        } catch (\Exception $exception) {
            $this->logger->logExceptions->critical(json_trace($exception));
            $this->addResponse($exception->getMessage(), 1, []);
            var_dump($exception);die();
            // Catch unexpected exceptions
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
            var_dump($exception);die();

            // All instances of OAuthServerException can be formatted into a HTTP response
            return $exception->generateHttpResponse($serverResponse);

        } catch (\Exception $exception) {
            var_dump($exception);die();

            // Unknown exception
            $body = new Stream(fopen('php://temp', 'r+'));
            $body->write($exception->getMessage());
            return $serverResponse->withStatus(500)->withBody($body);
        }
    }

    public function authCheck()
    {
        $this->accessTokenRepository = new AccessTokenRepository();

        $this->keys = $this->getAPIKeys();

        try {
            $this->resource = new ResourceServer(
                $this->accessTokenRepository,
                new CryptKey($this->keys['public_location'], $this->keys['pki_passphrase']),
                new BearerTokenValidator(
                    $this->accessTokenRepository
                )
            );

            $validateToken = $this->resource->validateAuthenticatedRequest(ServerRequest::fromGlobals());

            $this->headerAttributes = $validateToken->getAttributes();

            if ($this->accessTokenRepository->isTokenExpired($this->headerAttributes['oauth_access_token_id'])) {
                throw new \Exception('Token Expired!');
            }

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

        if ($this->auth->account()['security']['role_id'] == '1') {
            return array_merge($passwordGrant, $otherGrants);
        }

        return $otherGrants;
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

    public function getAPIAvailableScopes()
    {
        return $this->scopes->init()->scopes;
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

        if (!isset($data['type'])) {
            $this->addResponse('Please set url type.', 1);

            return false;
        }

        $this->validation->add('app_id', PresenceOf::class, ["message" => "Please provide app id."]);
        $this->validation->add('domain_id', PresenceOf::class, ["message" => "Please provide domain id."]);

        if (($validatedMessages = $this->validateData($data)) !== true) {
            $this->addResponse($validatedMessages, 1);

            return false;
        }

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
            $this->validation->add('client_id', PresenceOf::class, ["message" => "Please provide client id."]);
            $this->validation->add('redirect_url', PresenceOf::class, ["message" => "Please provide redirect URL."]);
            $this->validation->add('scope_id', PresenceOf::class, ["message" => "Please provide scope id."]);

            if (($validatedMessages = $this->validateData($data)) !== true) {
                $this->addResponse($validatedMessages, 1);

                return false;
            }

            $scope = $this->scopes->getById($data['scope_id']);
            if (!$scope) {
                $this->addResponse('Scope not found', 1);

                return false;
            }

            $url = $url . 'register/q/';

            if ($data['type'] === 'authorization') {
                $url = $url . 'csrf/' . $data['csrf'] . '/?response_type=code&client_id=' . $data['client_id'] . '&redirect_url=' . $data['redirect_url'];
                if (isset($data['state']) && $data['state'] !== '') {
                    $url = $url . '&state=' . $data['state'];
                }
            } else {
                $url = $url . 'response_type/code/client_id/' . $data['client_id'] . '/scope/' . $scope['scope_name'];

                $url = $url . '/state/' . ($data['state'] ?? '{{your_state_code}}');

                $url = $url . '/redirect_uri/__' . $data['redirect_url'] . '__';
            }

            $this->addResponse('Generated Url', 0, ['url' => $url]);
        } else if ($data['type'] === 'redirect') {
            if (($validatedMessages = $this->validateData($data)) !== true) {
                $this->addResponse($validatedMessages, 1);

                return false;
            }
            $url = $url . 'register/q/authorized/true';

            $this->addResponse('Generated Url', 0, ['url' => $url]);
        }

        return $url;
    }

    protected function validateData($data)
    {
        $validated = $this->validation->validate($data)->jsonSerialize();

        if (count($validated) > 0) {
            $messages = 'Error: ';

            foreach ($validated as $key => $value) {
                $messages .= $value['message'] . ' ';
            }
            return $messages;
        } else {
            return true;
        }
    }

    public function checkAuthorizationLinkData($getData)
    {
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
            } else if (!isset($getData['csrf'])) {
                $client = $this->clients->getFirst('client_id', $getData['client_id']);

                if (!$client || ($client && $client->revoked != 0)) {
                    throw new \Exception('Client ID is incorrect.');
                }

                $api = $this->getById($client->api_id);

                if ($api && isset($this->getData()['refresh']) && $this->getData()['refresh'] == true) {
                    $this->api = $api;

                    return $api;
                }

                if (isset($getData['state']) && $getData['state'] !== '') {
                    $api['state'] = $getData['state'];
                } else {
                    $api['state'] = null;
                }

                if (!$api || ($api && $api['grant_type'] !== 'authorization_code')) {
                    throw new \Exception('No API associated with this client ID.');
                }

                $scope = $this->scopes->getById($api['scope_id']);

                if ($scope && $scope['scope_name'] !== $getData['scope']) {
                    throw new \Exception('Scope is incorrect.');
                }

                $uri = $this->request->getUri();
                preg_match('/__.*/', $uri, $redirectUrl);
                if (isset($redirectUrl) && is_array($redirectUrl) && count($redirectUrl) === 1 && $redirectUrl[0] !== '') {
                    $redirectUrl = str_replace('__', '', $redirectUrl[0]);
                    if ($redirectUrl !== $client->redirectUri) {
                        throw new \Exception('Redirect URI is incorrect.');
                    }

                    $api['redirect_url'] = $redirectUrl;

                    $testRedirectUrl = $this->remoteWebContent->request('GET', $redirectUrl, ['timeout' => 1]);

                    if ($testRedirectUrl->getStatusCode() !== 200) {
                        throw new \Exception('Redirect URI is incorrect. Error Code: ' . $testRedirectUrl->getStatusCode());
                    }
                } else {
                    throw new \Exception('Redirect URI is incorrect.');
                }

                $api['csrf'] = $this->secTools->random->base58(32);

                $this->update($api);

                $api['type'] = 'authorization';

                $api['authorization_url'] = $this->generateAPIUrl($api);

                $this->api = $api;
            } else {
                $api = $this->getFirst('csrf', $getData['csrf'], false, false, null, [], true);

                if ($getData['csrf'] !== $api['csrf']) {
                    throw new \Exception('CSRF mismatch. Restart authorization process!');
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
}