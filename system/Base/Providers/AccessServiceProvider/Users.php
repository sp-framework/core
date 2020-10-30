<?php

namespace System\Base\Providers\AccessServiceProvider;

use System\Base\BasePackage;
use System\Base\Providers\AccessServiceProvider\Model\Users as UsersModel;

class Users extends BasePackage
{
    protected $modelToUse = UsersModel::class;

    protected $packageName = 'users';

    public $users;

    public function register(array $data)
    {
        if ($this->checkUserByEmail($data['email'])) {
            return false;
        }

        $validated = $this->validated($data);

        if ($validated) {
            $data['password'] =
                $this->hashPassword($data['password']);

            $newUser = $this->add($data);

            if ($newUser) {
                return true;
            }
        }

        return false;
    }

    protected function checkUserByEmail(string $email)
    {
        return
            $this->getByParams(
                    [
                        'conditions'    => 'email = :email:',
                        'bind'          =>
                            [
                                'email'  => $email
                            ]
                    ],
                    false,
                    false
                );
    }

    protected function hashPassword(string $password)
    {
        try {
            return $this->security->hash($password);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getModelsColumnMap()
    {
        $metaData = $this->getModelsMetaData();

        if ($metaData) {
            $columns = $metaData->getAttributes(new $this->modelToUse());

            unset($columns[2]);//Password
            unset($columns[4]);//Token_remember
            unset($columns[5]);//Token_identifier

            return $columns;
        }

        return false;
    }

    public function getAll(bool $resetCache = false, bool $enableCache = true)
    {
        if ($enableCache) {
            $parameters = $this->cacheTools->addModelCacheParameters([], $this->getCacheKey());
        } else {
            $parameters = [];
        }

        if (!$this->config->cache->enabled) {
            $parameters = [];
        }

        $parameters['columns'] = ['id', 'email', 'can_login'];

        if (!$this->{$this->packageName} || $resetCache) {

            $this->model = $this->modelToUse::find($parameters);

            $this->{$this->packageName} = $this->model->toArray();
        }

        return $this;
    }
}