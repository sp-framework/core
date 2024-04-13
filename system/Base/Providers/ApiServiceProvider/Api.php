<?php

namespace System\Base\Providers\ApiServiceProvider;

use DateInterval;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToReadFile;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\AuthorizationValidators\BearerTokenValidator;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\ResourceServer;
use System\Base\BasePackage;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApi;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiClients;
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

    public $api;

    public $scopes;

    protected $app;

    protected $keys;

    protected $response;

    public $isApi;

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

    public function init()
    {
        $this->scopes = new Scopes;

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

        if ($this->add($data)) {
            $newApi = $this->packagesData->last;

            $newApi = $this->generatePKIKeys($newApi);

            if ($newApi) {
                $this->updateApi($newApi);

                $this->addResponse('Added ' . $data['name'] . ' api');
            } else {
                $this->removeApi($newApi);

                return false;
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

        $data = array_merge($api, $data);

        if (isset($data['regenerate_pki_keys']) && $data['regenerate_pki_keys'] == 1) {
            $data = $this->generatePKIKeys($data);
        }

        if ($this->update($data)) {
            $this->addResponse('Updated ' . $data['name'] . ' api');
        } else {
            $this->addResponse('Error updating api.', 1);
        }
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

        if ($this->request->getBestAccept() === 'application/json') {
            $url = $this->request->getURI();
            $urlParts = explode("/", $url);

            if (isset($urlParts[1]) && $urlParts[1] === 'api' ||
                $this->request->getHeader('Authorization') !== '' ||
                $this->request->get('client_id') ||
                $this->request->get('grant_type')
            ) {
                $this->isApi = true;
            }
        }

        return $this->isApi;
    }

    public function getApiInfo()
    {
        if ($this->request->get('client_id')) {

        }
        if ($this->config->databasetype === 'db') {
            $api =
                $this->getByParams(
                    [
                        'conditions'    => 'domain_id = :did: AND app_id = :aid:',
                        'bind'          => [
                            'did'       => $this->domains->domain['id'],
                            'aid'       => $this->apps->getAppInfo()['id']
                        ]
                    ], true
                );
        } else {
            $api = $this->getByParams(['conditions' => [['domain_id', '=', (int) $this->domains->domain['id']], ['app_id', '=', (int) $this->apps->getAppInfo()['id']]]]);
        }

        var_dump($api);die();
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
        // $deviceCodeRepository = new DeviceCodeRepository();

        $this->keys = $this->api->getAPIKeys();

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

    // protected function initDcg()//Implemented in Oauth version 9.x
    // {
    //     // Enable the device code grant on the server with a token TTL of 1 hour
    //     $this->server->enableGrantType(
    //         new DeviceCodeGrant(
    //             $deviceCodeRepository,
    //             $refreshTokenRepository,
    //             new DateInterval('PT10M'),
    //             'http://foo/bar'
    //         ),
    //         new DateInterval($this->api['access_token_timeout'] ?? 'PT1H')
    //     );
    // }

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

    public function registerClient($data)
    {
        $serverResponse = new Response();

        try {
            // Try to respond to the access token request
            return $this->server->respondToAccessTokenRequest(ServerRequest::fromGlobals(), $serverResponse);
        } catch (OAuthServerException $exception) {
            $this->logger->logExceptions->critical($exception);
            var_dump($exception);die();
            // All instances of OAuthServerException can be converted to a PSR-7 response
            return $exception->generateHttpResponse($serverResponse);
        } catch (\Exception $exception) {
            $this->logger->logExceptions->critical($exception);
            var_dump($exception);die();
            // Catch unexpected exceptions
            $body = $serverResponse->getBody();
            $body->write($exception->getMessage());

            return $serverResponse->withStatus(500)->withBody($body);
        }
    }

    public function authCheck()
    {
        $this->accessTokenRepository = new AccessTokenRepository();

        $this->keys = $this->api->getAPIKeys();

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
            if (!$this->accessTokenRepository->isTokenExpired($this->headerAttributes['oauth_access_token_id'])) {
                throw new \Exception('Token Expired!');
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getAccount()
    {
        return $this->accessTokenRepository->getUserFromToken($this->headerAttributes['oauth_access_token_id']);
    }

    public function getAvailableAPIGrantTypes()
    {
        return
            [
                'password'    =>
                    [
                        'id'            => 'password',
                        'name'          => 'Password Grant (With Refresh Token)',
                    ],
                'client_credentials'   =>
                    [
                        'id'            => 'client_credentials',
                        'name'          => 'Client Credential Grant'
                    ],
                // 'device_code'   =>//Implemented in OAuth ver 9.x
                //  [
                //      'id'            => 'device_code',
                //      'name'          => 'Device Code Grant'
                //  ],
                'authorization_code'    =>
                    [
                        'id'            => 'authorization_code',
                        'name'          => 'Authorization Code Grant (With Refresh Token)',
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
        } catch (FilesystemException | UnableToReadFile $exception) {
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

    public function getAPIKeys($api)
    {
        $keys = [];

        try {
            $keys['enc'] = $this->secTools->decryptBase64($this->localContent->read('system/.api/' . $api['id'] . '/.enc'));
            $keys['public'] = $this->localContent->read('system/.api/' . $api['id'] . '/.public');
            $keys['public_location'] = base_path('system/.api/' . $api['id'] . '/.public');
            $keys['private'] = $this->localContent->read('system/.api/' . $api['id'] . '/.private');
            $keys['private_location'] = base_path('system/.api/' . $api['id'] . '/.private');
            $keys['pki'] = $this->localContent->read('system/.api/' . $api['id'] . '/.pki');
            $keys['pki_location'] = base_path('system/.api/' . $api['id'] . '/.pki');
            $keys['pki_passphrase'] = $this->secTools->decryptBase64($api['private_key_passphrase']);
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

    public function generateClientKeys()
    {
        $newClient['app_id'] = $this->api['id'];
        $newClient['domain_id'] = $this->domains->domain['id'];
        $newClient['account_id'] = $this->auth->account()['id'];
        $newClient['name'] = $newClient['app_id'] . '_' . $newClient['domain_id'] . '_' . $newClient['account_id'];
        $newClient['client_id'] = $this->random->base58(isset($this->api['client_id_length']) ? $this->api['client_id_length'] : 8);
        $client_secret = $this->random->base58(isset($this->api['client_secret_length']) ? $this->api['client_secret_length'] : 32);
        $newClient['client_secret'] = $this->secTools->hashPassword($client_secret);
        $newClient['redirect_uri'] = 'https://';
        $newClient['grant_types'] = '';
        $newClient['scope'] = '*';
        // $newClient['created_at'] = time();
        // $newClient['updated_at'] = time();

        try {
            $clientsObject = new ServiceProviderApiClients;
            $clientsStore = $this->ff->store($clientsObject->getSource());

            if ($this->config->databasetype === 'db') {
                $oldClientsObj = $clientsObject->findFirstByName($newClient['name']);

                if ($oldClientsObj) {
                    $oldClient = $oldClientsObj->toArray();
                }
            } else {
                $oldClient = $clientsStore->findOneBy(['name', '=', $newClient['name']]);
            }

            if (isset($oldClient)) {
                $newClient = array_merge($oldClient, $newClient);

                if ($this->config->databasetype === 'db') {
                    $oldClientsObj->assign($newClient);

                    $oldClientsObj->update();
                } else {
                    $clientsStore->update($newClient);
                }

                $this->addResponse('Keys regenerated successfully.', 0, ['client_id' => $newClient['client_id'], 'client_secret' => $client_secret]);
            } else {
                if ($this->config->databasetype === 'db') {
                    $clientsObject->assign($newClient);

                    $clientsObject->create();
                } else {
                    $clientsStore->insert($newClient);
                }

                $this->addResponse('Keys generated successfully.', 0, ['client_id' => $newClient['client_id'], 'client_secret' => $client_secret]);
            }
        } catch (\Exception $e) {
            $this->addResponse('Error generating/updating keys. Please contact administrator.', 1);
        }
    }

    public function getAPIAvailableScopes()
    {
        return $this->scopes->init()->scopes;
    }
}