<?php

namespace System\Base\Providers\ApiServiceProvider;

use System\Base\BasePackage;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiClients;

class Clients extends BasePackage
{
    protected $modelToUse = ServiceProviderApiClients::class;

    protected $packageName = 'clients';

    public $clients;

    public function init(bool $resetCache = false)
    {
        return $this;
    }

    public function addClient(array $data, $viaRegister = false)
    {
        if (!isset($data['client_id'])) {
            $validation = $this->basepackages->accounts->validateData($data, true);

            if ($validation !== true) {
                $this->addResponse($validation, 1);

                return;
            }

            $api = $this->api->getById($data['api_id']);

            if (!$api) {
                $this->addResponse('API Id incorrect!', 1);

                return;
            }

            if ($api['grant_type'] === 'password' && !$viaRegister) {
                $account = $this->basepackages->accounts->checkAccountBy($data['email']);

                if (!$account) {
                    $this->addResponse('API grant type password needs an account in the system. No account with that email exists!', 1);

                    return;
                }

                $oldClient = $this->checkClientExists($api, $account);

                if ($oldClient && isset($oldClient[0]['id'])){
                    if (isset($data['forceRegen']) && (bool) $data['forceRegen'] === true) {
                        $data['id'] = $oldClient[0]['id'];
                        if (isset($data['forceRegen'])) {
                            unset($data['forceRegen']);//We unset to avoid loop
                        }
                        $data['forceRevoke'] = true;//We set to avoid loop
                        if (!$this->forceRevoke($data, $oldClient[0])) {
                            $this->addResponse('Error revoking old keys. Please contact administrator.', 1);

                            return false;
                        }
                        if (isset($data['forceRevoke'])) {
                            unset($data['forceRevoke']);//unset, so we dont check again on generate client keys
                        }
                    } else {
                        $this->addResponse('Client exists, but not revoked. Use force!', 1);

                        return false;
                    }
                }
            } else if ($api['grant_type'] === 'client_credentials') {
                $account['email'] = $data['email'];

                if ($viaRegister) {
                    $account['id'] = 0;
                }

                if (isset($data['device_id']) && (bool) $data['device_id'] === true) {
                    //Check the amount of client allowed
                    $clientsCount = $this->checkCCAccountDeviceCount($api, $account);

                    if (isset($api['cc_max_devices']) && (int) $api['cc_max_devices'] >= 0) {
                        if ($api['cc_max_devices'] === 0) {
                            $this->addResponse('Device registration for this API is disabled.', 1);

                            return false;
                        }

                        if ($clientsCount >= $api['cc_max_devices']) {
                            $this->addResponse('Max clients for this account reached. Revoke old clients to add new clients.', 1);

                            return false;
                        }
                    }
                    $account['device_id'] = $this->random->base58(isset($api['client_id_length']) ? $api['client_id_length'] : 8);
                } else {
                    $oldClient = $this->checkClientExists($api, $account);

                    if ($oldClient && isset($oldClient[0]['id'])){
                        if (isset($data['forceRegen']) && (bool) $data['forceRegen'] === true) {
                            $data['id'] = $oldClient[0]['id'];
                            if (isset($data['forceRegen'])) {
                                unset($data['forceRegen']);//We unset to avoid loop
                            }
                            $data['forceRevoke'] = true;//We set to avoid loop
                            if (!$this->forceRevoke($data, $oldClient[0])) {
                                $this->addResponse('Error revoking old keys. Please contact administrator.', 1);

                                return false;
                            }
                            if (isset($data['forceRevoke'])) {
                                unset($data['forceRevoke']);//unset, so we dont check again on generate client keys
                            }
                        } else {
                            $this->addResponse('Client exists, but not revoked. Use force!', 1);

                            return false;
                        }
                    }
                }
            }

            $this->generateClientKeys($data, $account);

            return true;
        }

        $this->resetCallsCount([], $data);

        if ($this->add($data)) {
            $this->addResponse('Added client');

            return true;
        } else {
            $this->addResponse('Error adding client.', 1);
        }
    }

    /**
     * @notification(name=update)
     */
    public function updateClient(array $data)
    {
        if ($this->update($data)) {
            $this->addResponse('Updated client');

            return true;
        } else {
            $this->addResponse('Error updating client.', 1);
        }
    }

    /**
     *
     */
    protected function removeClient(array $data)
    {
        //
    }

    public function forceRevoke(array $data = null, $client = null)
    {
        if (!isset($data['id'])) {
            $this->addResponse('Client ID not set', 1);

            return false;
        }

        if (!$client) {
            $client = $this->getById($data['id']);
        }

        if ($client) {
            $api = $this->api->getById($client['api_id']);

            if ($api['is_public'] == true) {
                $this->addResponse('Cannot revoke or generate key for public client.', 1);

                return false;
            }

            if (isset($data['forceRegen']) && (bool) $data['forceRegen'] === true) {
                $account = $this->basepackages->accounts->getById($client['account_id']);

                if ($client['device_id']) {
                    $account['device_id'] = $client['device_id'];
                }
                $data['api_id'] = $client['api_id'];

                $this->generateClientKeys($data, $account);

                $this->addResponse('Revoked & Regenerated client');

                return true;
            } else if (isset($data['forceRevoke']) && $data['forceRevoke'] == 'true') {
                if ($client['revoked'] == '1') {
                    $this->addResponse('Client already revoked!', 1);

                    return false;
                }

                $client['revoked'] = '1';

                $this->updateClient($client);

                $this->addResponse('Revoked client');

                return true;
            } else {
                $this->addResponse('Client exists, but not revoked. Use force!', 1);

                return false;
            }
        }

        $this->addResponse('Client does not exists.', 1);

        return false;
    }

    public function generateClientKeys($data, $account = null, $newClient = null, $emailNewClientDetails = true)
    {
        $api = $this->api->getById($data['api_id']);

        if ($api) {
            if (isset($data['forceRevoke']) || isset($data['forceRegen'])) {
                $oldClient = $this->checkClientExists($api, $account);

                if ($oldClient && isset($oldClient[0]['id']) && isset($data['forceRegen']) && (bool) $data['forceRegen'] === true) {
                    $data['id'] = $oldClient[0]['id'];
                    if (isset($data['forceRegen'])) {
                        unset($data['forceRegen']);//We unset to avoid loop
                    }
                    $data['forceRevoke'] = true;//We set to avoid loop
                    if (!$this->forceRevoke($data, $oldClient[0])) {
                        $this->addResponse('Error revoking old keys. Please contact administrator.', 1);

                        return false;
                    }
                }
            }

            $apiName = $api['id'] . '_' . $api['app_id'] . '_' . $api['domain_id'] . '_' . ($account['id'] ?? $this->auth->account()['id']);
            if ($account || $api['client_keys_generation_allowed'] == true) {
                $newClient['api_id'] = $api['id'];
                $newClient['app_id'] = $api['app_id'];
                $newClient['domain_id'] = $api['domain_id'];
                $newClient['account_id'] = $account['id'] ?? $this->auth->account()['id'];
                $newClient['email'] = $account['email'] ?? $this->auth->account()['email'];
                $newClient['name'] = $apiName;
                $newClient['client_id'] = $data['client_id'] ?? $this->random->base58(isset($api['client_id_length']) ? $api['client_id_length'] : 8);
                $client_secret = $data['client_secret'] ?? $this->random->base58(isset($api['client_secret_length']) ? $api['client_secret_length'] : 32);
                $newClient['client_secret'] = $this->secTools->hashPassword($client_secret);
                $newClient['last_used'] = (\Carbon\Carbon::now())->toDateTimeLocalString();
                $newClient['revoked'] = '0';
                $newClient['redirectUri'] = $data['redirect_url'] ?? 'https://';
                $newClient['device_id'] = null;
                if (isset($account['device_id'])) {
                    $newClient['device_id'] = $account['device_id'];
                }
                if (isset($api['redirect_uri'])) {
                    $newClient['redirectUri'] = $api['redirect_uri'];
                }
            }

            try {
                if (isset($newClient) && $this->addClient($newClient)) {
                    $this->addResponse('Keys generated successfully.', 0, ['client_id' => $newClient['client_id'], 'client_secret' => $client_secret]);

                    if ($emailNewClientDetails) {
                        $this->emailNewClientDetails($api, $newClient, $client_secret);
                    }

                    return $newClient;
                } else {
                    $this->addResponse('Error API does not allow client keys generation. Please contact administrator.', 1);
                }
            } catch (\Exception $e) {
                $this->addResponse('Error generating/updating keys. Please contact administrator.', 1);
            }

            return false;
        }

        $this->addResponse('Error API does not exists. Please contact administrator.', 1);

        return false;
    }

    protected function checkClientExists($api, $account)
    {
        if ($this->config->databasetype === 'db') {
            return $this->getByParams(
                [
                    'conditions'    => 'email = :email: AND revoked = :revoked: AND device = :device: AND api_id = :api_id:',
                    'bind'          =>
                        [
                            'email'         => ($account['email'] ?? $this->auth->account()['email']),
                            'revoked'       => '0',
                            'device_id'     => ($account['device_id'] ?? null),
                            'api_id'        => $api['id']
                        ]
                ]
            );
        } else {
            return $this->getByParams(
                [
                    'conditions' =>
                        [
                            ['email', '=', ($account['email'] ?? $this->auth->account()['email'])],
                            ['revoked', '=', false],
                            ['device_id', '=', ($account['device_id'] ?? null)],
                            ['api_id', '=', $api['id']]
                        ]
                ]
            );
        }

        return false;
    }

    protected function checkCCAccountDeviceCount($api, $account)
    {
        $clientsCount = 0;

        if ($this->config->databasetype === 'db') {
            $clients = $this->getByParams(
                [
                    'conditions'    => 'email = :email: AND revoked = :revoked: AND device != null AND api_id = :api_id:',
                    'bind'          =>
                        [
                            'email'         => $account['email'],
                            'revoked'       => false,
                            'api_id'        => $api['id']
                        ]
                ]
            );
        } else {
            $clients = $this->getByParams(
                [
                    'conditions' =>
                        [
                            ['email', '=', $account['email']],
                            ['revoked', '=', false],
                            ['device_id', '!=', null],
                            ['api_id', '=', $api['id']]
                        ]
                ]
            );
        }

        if (isset($clients) && $clients && is_array($clients)) {
            return count($clients);
        }

        return $clientsCount;
    }

    public function generateClientIdAndSecret($data)
    {
        if (isset($data['generate_client_id']) && $data['generate_client_id'] == true) {
            $clientId = $this->random->base58(isset($data['client_id_length']) ? $data['client_id_length'] : 8);
        }
        if (isset($data['generate_client_secret']) && $data['generate_client_secret'] == true) {
            $clientSecret = $this->random->base58(isset($data['client_secret_length']) ? $data['client_secret_length'] : 32);
        }

        $responseArr = [];
        if (isset($clientId)) {
            $responseArr['client_id'] = $clientId;
        }

        if (isset($clientSecret)) {
            $responseArr['client_secret'] = $clientSecret;
        }

        $this->addResponse('Id generated successfully.', 0, $responseArr);

        return $responseArr;
    }

    protected function emailNewClientDetails($api, $newClient, $clientSecret)
    {
        $domain = $this->domains->getById($api['domain_id']);

        $emailData['app_id'] = $api['app_id'];
        $emailData['domain_id'] = $api['domain_id'];
        $emailData['status'] = 1;
        $emailData['priority'] = 1;
        $emailData['confidential'] = 1;
        $emailData['to_addresses'] = $this->helper->encode([$newClient['email']]);
        $emailData['subject'] = 'API client details for ' . $domain['name'];
        $emailData['body'] = '';
        if ($newClient['device_id']) {
            $emailData['body'] = 'Device ID: ' . $newClient['device_id'] . '<br>';
        }
        $emailData['body'] = $emailData['body'] . 'Client ID: ' . $newClient['client_id'] . '<br>' . 'Client Secret: ' . $clientSecret;

        return $this->basepackages->emailqueue->addToQueue($emailData);
    }

    public function checkCallCount($types = [], &$client)
    {
        $toResetCallCount = [];

        if (in_array('per_minute_calls_count', $types)) {
            $perMinuteCallsStart = (\Carbon\Carbon::parse($client['per_minute_calls_start']));
            $now = \Carbon\Carbon::now();

            if ($perMinuteCallsStart->diffInMinutes($now) > 1) {
                array_push($toResetCallCount, 'per_minute_calls_count');
            }
        }

        if (in_array('per_hour_calls_count', $types)) {
            $perHourCallsStart = (\Carbon\Carbon::parse($client['per_hour_calls_start']));
            $now = \Carbon\Carbon::now();

            if ($perHourCallsStart->diffInMinutes($now) > 60) {
                array_push($toResetCallCount, 'per_hour_calls_count');
            }
        }

        if (in_array('per_day_calls_count', $types)) {
            $perDayCallsStart = (\Carbon\Carbon::parse($client['per_day_calls_start']));
            $now = \Carbon\Carbon::now();

            if ($perDayCallsStart->diffInHours($now) > 24) {
                array_push($toResetCallCount, 'per_day_calls_count');
            }
        }

        if (count($toResetCallCount) > 0) {
            $this->resetCallsCount($toResetCallCount, $client);
        }
    }

    public function incrementCallCount($types = [], &$client, $api)
    {
        if (in_array('per_minute_calls_count', $types)) {
            if ($client['per_minute_calls_count'] < (int) $api['per_minute_calls_limit']) {
                $client['per_minute_calls_count'] = $client['per_minute_calls_count'] + 1;
            }
        }

        if (in_array('per_hour_calls_count', $types)) {
            if ($client['per_hour_calls_count'] < (int) $api['per_hour_calls_limit']) {
                $client['per_hour_calls_count'] = $client['per_hour_calls_count'] + 1;
            }
        }

        if (in_array('per_day_calls_count', $types)) {
            if ($client['per_day_calls_count'] < (int) $api['per_day_calls_limit']) {
                $client['per_day_calls_count'] = $client['per_day_calls_count'] + 1;
            }
        }

        if (in_array('concurrent_calls_count', $types)) {
            if ((int) $client['concurrent_calls_count'] < (int) $api['concurrent_calls_limit']) {
                $client['concurrent_calls_count'] = $client['concurrent_calls_count'] + 1;
            }
        }

        if ($this->caching->enabled) {
            $this->caching->setCache('api-clients-' . $this->request->getClientAddress(), $client);
        }

        $this->update($client);
    }

    public function resetCallsCount($types = [], &$client = null, $clientId = null)
    {
        if (!$client && $clientId) {
            $client = $this->getById($clientId);

            if (!$client) {
                $this->addResponse('Client ID incorrect', 1);

                return;
            }
        }

        if (in_array('concurrent_calls_count', $types)) {
            $client['concurrent_calls_count'] = 0;
        }
        if (in_array('per_minute_calls_count', $types)) {
            $client['per_minute_calls_count'] = 0;
            $client['per_minute_calls_start'] = (\Carbon\Carbon::now()->startOfMinute()->toDateTimeLocalString());
        }
        if (in_array('per_hour_calls_count', $types)) {
            $client['per_hour_calls_count'] = 0;
            $client['per_hour_calls_start'] = (\Carbon\Carbon::now()->startOfHour()->toDateTimeLocalString());
        }
        if (in_array('per_day_calls_count', $types)) {
            $client['per_day_calls_count'] = 0;
            $client['per_day_calls_start'] = (\Carbon\Carbon::now()->startOfDay()->toDateTimeLocalString());
        }

        if (count($types) === 0) {
            $client['concurrent_calls_count'] = 0;
            $client['per_minute_calls_count'] = 0;
            $client['per_hour_calls_count'] = 0;
            $client['per_day_calls_count'] = 0;
            $client['per_minute_calls_start'] = (\Carbon\Carbon::now()->startOfMinute()->toDateTimeLocalString());
            $client['per_hour_calls_start'] = (\Carbon\Carbon::now()->startOfHour()->toDateTimeLocalString());
            $client['per_day_calls_start'] = (\Carbon\Carbon::now()->startOfDay()->toDateTimeLocalString());
        }

        if (isset($client['id'])) {
            $client['last_used'] = (\Carbon\Carbon::now())->toDateTimeLocalString();

            $this->caching->init('apcuCache', 7200);

            if ($this->caching->enabled) {
                $this->caching->setCache('api-clients-' . $this->request->getClientAddress(), $client);
            }

            $this->update($client);
        }
    }

    public function setClientsLastUsed($client, $setLastUsed = true, $decrementConcurrentCallsCounter = true)
    {
        $client = $this->getById($client['id']);//Important as we need the client from the DB.

        if ($setLastUsed) {
            $client['last_used'] = (\Carbon\Carbon::now())->toDateTimeLocalString();
        }

        if ($decrementConcurrentCallsCounter && (int) $client['concurrent_calls_count'] > 0) {
            $client['concurrent_calls_count'] = $client['concurrent_calls_count'] - 1;
        }

        if ($this->caching->enabled) {
            $this->caching->setCache('api-clients-' . $this->request->getClientAddress(), $client);
        }

        $this->update($client);
    }
}