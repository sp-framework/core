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
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' role';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new role.';
        }
    }

    public function updateRole(array $data)
    {
        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' role';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating role.';
        }
    }

    public function removeRole(array $data)
    {
        if (isset($data['id']) && $data['id'] != 1) {
            if ($this->remove($data['id'])) {
                //Check users assigned to the role
                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Removed role';
            } else {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error removing role.';
            }
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Cannot remove default role.';
        }
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