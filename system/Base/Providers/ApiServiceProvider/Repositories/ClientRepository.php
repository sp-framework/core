<?php

namespace System\Base\Providers\ApiServiceProvider\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use System\Base\BasePackage;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiClients;
use System\Base\Providers\ApiServiceProvider\Repositories\Repository;

class ClientRepository extends BasePackage implements ClientRepositoryInterface
{
    protected $modelToUse = ServiceProviderApiClients::class;

    protected $client;

    public function getClientEntity($clientIdentifier): ?ClientEntityInterface
    {
        $clientObj = $this->getFirst('client_id', $clientIdentifier);

        if ($clientObj) {
            $this->client = $clientObj->toArray();

            $clientObj = new $this->modelToUse;

            $clientObj->assign($this->client);

            $clientObj->setConfidential();

            return $clientObj;
        }

        return false;
    }

    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        $this->getClientEntity($clientIdentifier);

        if ($this->client) {
            if ($this->secTools->checkPassword($clientSecret, $this->client['client_secret'])) {
                return true;
            }
        }

        return false;
    }
}