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

    public function generateClientKeys($data, $account = null, $newClient = null, $emailNewClientDetails = true, $clientID = null)
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
                $newClient['client_id'] = $clientID ?? $this->random->base58(isset($api['client_id_length']) ? $api['client_id_length'] : 8);
                $client_secret = $this->random->base58(isset($api['client_secret_length']) ? $api['client_secret_length'] : 32);
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
                            'revoked'       => false,
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

    public function generateClientId($data)
    {
        $clientId = $this->random->base58(isset($data['client_id_length']) ? $data['client_id_length'] : 8);

        $this->addResponse('Id generated successfully.', 0, ['client_id' => $clientId]);

        return $clientId;
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

    public function incrementCallCount($data, $type)
    {
        //To increment call counts of the client
    }

    public function resetCallCount($clientId, $type)
    {
        //To reset call counts of the client
    }
}