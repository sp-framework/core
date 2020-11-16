<?php

namespace System\Base\Providers\AccessServiceProvider;

use System\Base\BasePackage;
use System\Base\Providers\AccessServiceProvider\Model\Roles as RolesModel;

class Roles extends BasePackage
{
    protected $modelToUse = RolesModel::class;

    protected $packageName = 'roles';

    public $roles;

    public function addRole(array $data)
    {
        if ($this->add($data)) {
            $this->packagesData->reponseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' role';
        } else {
            $this->packagesData->reponseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new role.';
        }
    }

    public function updateRole(array $data)
    {
        if ($this->update($data)) {
            $this->packagesData->reponseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' role';
        } else {
            $this->packagesData->reponseCode = 1;

            $this->packagesData->responseMessage = 'Error updating new role.';
        }
    }

    public function removeRole(array $data)
    {
        //Check users assigned first.
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