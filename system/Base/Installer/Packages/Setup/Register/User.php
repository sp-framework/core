<?php

namespace System\Base\Installer\Packages\Setup\Register;

use Phalcon\Helper\Json;

class User
{
    public function register($db, $email, $password, $newApplicationId)
    {
        $canLogin = Json::encode([$newApplicationId => true]);

        $db->insertAsDict(
            'users',
            [
                'email'                 => $email,
                'password'              => $password,
                'can_login'             => $canLogin
            ]
        );
    }
}