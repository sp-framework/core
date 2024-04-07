<?php

namespace System\Base\Providers\ApiServiceProvider\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersAccounts;

class UserRepository extends BasePackage implements UserRepositoryInterface
{
    protected $modelToUse = BasepackagesUsersAccounts::class;

    protected $user;

    public function modelName()
    {
        return BasepackagesUsersAccounts::class;
    }

    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        $userObj = $this->getFirst('email', $username);

        if ($userObj) {
            $this->user = $userObj->toArray();
        }

        $this->user = $this->basepackages->accounts->getAccountById($this->user['id']);

        if ($this->secTools->checkPassword($password, $this->user['security']['password'])) {
            $userObj = new $this->modelToUse;

            return $userObj;
        }

        return false;
    }
}