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

    public function addClient(array $data)
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

            if ($api['grant_type'] === 'password') {
                $account = $this->basepackages->accounts->checkAccountBy($data['email']);

                if (!$account) {
                    $this->addResponse('API grant type password needs an account in the system. No account with that email exists!', 1);

                    return;
                }
            } else if ($api['grant_type'] === 'client_credentials') {
                $account['email'] = $data['email'];
            }

            $this->generateClientKeys($api, $account);

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

    public function forceRevoke(array $data)
    {
        if (isset($data['regen']) && $data['regen'] == 'true' && isset($data['id'])) {
            $client = $this->getById($data['id']);
            $api = $this->api->getById($client['api_id']);
            $account = $this->basepackages->accounts->getById($client['account_id']);

            $this->generateClientKeys($api, $account);

            $this->addResponse('Revoked & Regenerated client');

            return true;
        } else if (isset($data['id'])) {
            $client = $this->getById($data['id']);

            if ($client['revoked'] == '1') {
                $this->addResponse('Client already revoked!', 1);

                return false;
            }

            $client['revoked'] = '1';

            $this->updateClient($client);

            $this->addResponse('Revoked client');

            return true;
        }

        $this->addResponse('Id not set', 1);

        return false;
    }

    public function generateClientKeys($api, $account = null, $newClient = null, $emailNewClientDetails = true)
    {
        $api = $this->api->getById($api['id']);

        if ($api) {
            $apiName = $api['id'] . '_' . $api['app_id'] . '_' . $api['domain_id'] . '_' . ($account['id'] ?? $this->auth->account()['id']);
            if ($account || $api['client_keys_generation_allowed'] == true) {
                $newClient['api_id'] = $api['id'];
                $newClient['app_id'] = $api['app_id'];
                $newClient['domain_id'] = $api['domain_id'];
                $newClient['account_id'] = $account['id'] ?? $this->auth->account()['id'];
                $newClient['email'] = $account['email'] ?? $this->auth->account()['email'];
                $newClient['name'] = $apiName;
                $newClient['client_id'] = $this->random->base58(isset($api['client_id_length']) ? $api['client_id_length'] : 8);
                $client_secret = $this->random->base58(isset($api['client_secret_length']) ? $api['client_secret_length'] : 32);
                $newClient['client_secret'] = $this->secTools->hashPassword($client_secret);
                $newClient['last_used'] = (\Carbon\Carbon::now())->toDateTimeLocalString();
                $newClient['revoked'] = '0';
                $newClient['redirectUri'] = 'https://';//Change this to default URI
                if (isset($api['redirect_uri'])) {
                    $newClient['redirectUri'] = $api['redirect_uri'];
                }
            }

            try {
                if ($this->config->databasetype === 'db') {
                    $oldClient = $this->getByParams(
                        [
                            'conditions'    => 'name = :name: AND revoked = :revoked:',
                            'bind'          =>
                                [
                                    'name'      => $apiName,
                                    'revoked'   => false
                                ]
                        ]
                    );
                } else {
                    $oldClient = $this->getByParams(['conditions' => [['name', '=', $apiName], ['revoked', '=', false]]]);
                }

                if ($oldClient && isset($oldClient[0]['id'])) {
                    $oldClient[0]['revoked'] = true;

                    if (!$this->updateClient($oldClient[0])) {
                        return false;
                    }
                }

                if (isset($newClient) && $this->addClient($newClient)) {
                    $this->addResponse('Keys generated successfully.', 0, ['client_id' => $newClient['client_id'], 'client_secret' => $client_secret]);

                    if ($emailNewClientDetails) {
                        $this->emailNewClientDetails($api, $newClient['email'], $newClient['client_id'], $client_secret);
                    }
                    return true;
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

    public function generateClientId($data)
    {
        $this->addResponse('Id generated successfully.', 0, ['client_id' => $this->random->base58(isset($data['client_id_length']) ? $data['client_id_length'] : 8)]);
    }

    protected function emailNewClientDetails($api, $email, $clientId, $clientSecret)
    {
        $domain = $this->domains->getById($api['domain_id']);

        $emailData['app_id'] = $api['app_id'];
        $emailData['domain_id'] = $api['domain_id'];
        $emailData['status'] = 1;
        $emailData['priority'] = 1;
        $emailData['confidential'] = 1;
        $emailData['to_addresses'] = $this->helper->encode([$email]);
        $emailData['subject'] = 'API client details for ' . $domain['name'];
        $emailData['body'] = 'Client ID: ' . $clientId . '<br>' . 'Client Secret: ' . $clientSecret;

        return $this->basepackages->emailqueue->addToQueue($emailData);
    }
}