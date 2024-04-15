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

    /**
     * Only via generate new client via profile.
     */
    protected function addClient(array $data)
    {
        if ($this->add($data)) {
            $this->addResponse('Added ' . $data['name'] . ' client');

            return true;
        } else {
            $this->addResponse('Error updating client.', 1);
        }
    }

    /**
     * @notification(name=update)
     */
    public function updateClient(array $data)
    {
        if ($this->update($data)) {
            $this->addResponse('Updated ' . $data['name'] . ' client');

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
        if (isset($data['id'])) {
            $client = $this->getById($data['id']);

            if ($client['revoked'] == '1') {
                $this->addResponse('Client already revoked!', 1);

                return false;
            }

            $client['revoked'] = '1';

            $this->updateClient($client);

            $this->addResponse('Revoked ' . $client['name'] . ' client');

            return true;
        }

        $this->addResponse('Id not set', 1);

        return false;
    }

    public function generateClientKeys($api)
    {
        $newClient['api_id'] = $api['id'];
        $newClient['app_id'] = $this->apps->getAppInfo()['id'];
        $newClient['domain_id'] = $this->domains->domain['id'];
        $newClient['account_id'] = $this->auth->account()['id'];
        $newClient['name'] = $newClient['app_id'] . '_' . $newClient['domain_id'] . '_' . $newClient['account_id'];
        $newClient['client_id'] = $this->random->base58(isset($api['client_id_length']) ? $api['client_id_length'] : 8);
        $client_secret = $this->random->base58(isset($api['client_secret_length']) ? $api['client_secret_length'] : 32);
        $newClient['client_secret'] = $this->secTools->hashPassword($client_secret);
        $newClient['redirectUri'] = 'https://';//Change this to default URI
        $newClient['last_used'] = (\Carbon\Carbon::now())->toDateTimeLocalString();
        $newClient['revoked'] = '0';
        if (isset($api['redirect_uri'])) {
            $newClient['redirectUri'] = $api['redirect_uri'];
        }

        try {
            if ($this->config->databasetype === 'db') {
                $oldClient = $this->getByParams(
                    [
                        'conditions'    => 'name = :name: AND revoked = :revoked:',
                        'bind'          =>
                            [
                                'name'      => $newClient['name'],
                                'revoked'   => false
                            ]
                    ]
                );
            } else {
                $oldClient = $this->getByParams(['conditions' => [['name', '=', $newClient['name']], ['revoked', '=', false]]]);
            }

            if ($oldClient && isset($oldClient[0]['id'])) {
                $oldClient[0]['revoked'] = true;

                if (!$this->updateClient($oldClient[0])) {
                    return false;
                }
            }

            if (!$this->addClient($newClient)) {
                return false;
            }

            $this->addResponse('Keys generated successfully.', 0, ['client_id' => $newClient['client_id'], 'client_secret' => $client_secret]);
        } catch (\Exception $e) {
            $this->addResponse('Error generating/updating keys. Please contact administrator.', 1);
        }
    }

    public function generateClientId($data)
    {
        $this->addResponse('Id generated successfully.', 0, ['client_id' => $this->random->base58(isset($data['client_id_length']) ? $data['client_id_length'] : 8)]);
    }
}