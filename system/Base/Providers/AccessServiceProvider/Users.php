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
}