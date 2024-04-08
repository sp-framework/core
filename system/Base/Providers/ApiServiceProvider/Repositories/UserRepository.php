<?php

namespace System\Base\Providers\ApiServiceProvider\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use System\Base\BasePackage;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApiUsers;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersAccounts;

class UserRepository extends BasePackage implements UserRepositoryInterface
{
    protected $modelToUse = ServiceProviderApiUsers::class;

    protected $user;

    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        $this->modelToUse = BasepackagesUsersAccounts::class;

        $this->setFfStoreToUse();

        $userObj = $this->getFirst('email', $username);

        if ($userObj) {
            $this->user = $userObj->toArray();
        }
        $this->user = $this->basepackages->accounts->getAccountById($this->user['id']);

        if ($this->secTools->checkPassword($password, $this->user['security']['password'])) {
            $userObj = new ServiceProviderApiUsers;

            $userObj->app_id = $this->apps->getAppInfo()['id'];
            $userObj->domain_id = $this->domains->domain['id'];
            $userObj->account_id = $this->user['id'];
            $userObj->scope = '';

            return $userObj;
        }

        return false;
    }
}