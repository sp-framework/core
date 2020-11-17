<?php

namespace System\Base\Providers\AccessServiceProvider;

use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use System\Base\BasePackage;
use System\Base\Providers\AccessServiceProvider\Model\Users as UsersModel;

class Users extends BasePackage
{
    protected $modelToUse = UsersModel::class;

    protected $packageName = 'users';

    public $users;

    public function addUser(array $data)
    {
        $data['email'] = strtolower($data['email']);

        if ($this->checkUserByEmail($data['email'])) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Account already exists!';

            return false;
        }

        if ($this->validateData($data)) {
            if (isset($data['password'])) {
                $data['password'] =
                    $this->secTools->hashPassword($data['password']);
            } else {
                $data['password'] =
                    $this->secTools->hashPassword($this->random->base62(12));
            }

            $newUser = $this->add($data);

            if ($newUser) {
                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Account added';

                return true;
            }
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Account already exists';

            return false;
        }

        return true;
    }

    public function updateUser(array $data)
    {
        $data['email'] = strtolower($data['email']);

        if ($this->update($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['email'] . ' user';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating user.';
        }
    }

    public function removeUser(array $data)
    {
        if (isset($data['id']) && $data['id'] != 1) {
            if ($this->remove($data['id'])) {
                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Removed user';
            } else {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error removing user.';
            }
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Cannot remove default user.';
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

    protected function validateData(array $data)
    {
        $this->validation->add('email', PresenceOf::class, ["message" => "Enter valid username."]);
        $this->validation->add('email', Email::class, ["message" => "Enter valid username."]);

        $validated = $this->validation->validate($data)->jsonSerialize();

        if (count($validated) > 0) {
            $messages = 'Error: ';

            foreach ($validated as $key => $value) {
                $messages .= $value['message'] . ' ';
            }
            return $messages;
        } else {
            return true;
        }
    }
}