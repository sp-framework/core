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
                'contact_address_ids'   => Json::encode([]),
                'contact_phone'         => '',
                'contact_phone_ext'     => '',
                'contact_mobile'        => '',
                'contact_fax'           => '',
                'contact_other'         => '',
                'contact_notes'         => ''
            ]
        );
    }
}