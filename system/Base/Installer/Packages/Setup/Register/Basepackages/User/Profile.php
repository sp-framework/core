<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\User;

use Phalcon\Helper\Json;

class Profile
{
    public function register($db, $adminAccountId)
    {
        $db->insertAsDict(
            'basepackages_users_profiles',
            [
                'account_id'            => $adminAccountId,
                'first_name'            => 'System',
                'last_name'             => 'Administrator',
                'full_name'             => 'System Administrator',
                'contact_phone'         => '',
                'contact_mobile'        => ''
            ]
        );
    }
}