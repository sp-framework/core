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
                $this->secTools->hashPassword($data['password']);

            $newUser = $this->add($data);

            if ($newUser) {
                return true;
            }
        }

        return false;
    }

    public function checkUserByEmail(string $email)
    {
        $user =
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

        if ($user) {
            return $user[0];
        } else {
            return false;
        }
    }

    public function checkUserByIdentifier(string $rememberIdentifier)
    {
        $user =
            $this->getByParams(
                    [
                        'conditions'    => 'remember_identifier = :ri:',
                        'bind'          =>
                            [
                                'ri'  => $rememberIdentifier
                            ]
                    ],
                    false,
                    false
                );

        if ($user) {
            return $user[0];
        } else {
            return false;
        }
    }
}