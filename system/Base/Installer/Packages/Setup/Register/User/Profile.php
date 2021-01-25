<?php

namespace System\Base\Installer\Packages\Setup\Register\User;

use Phalcon\Helper\Json;

class Profile
{
    public function register($db, $adminAccountId)
    {
        $db->insertAsDict(
            'profiles',
            [
                'account_id'            => $adminAccountId,
                'first_name'            => 'Administrator',
                'last_name'             => '',
                'full_name'             => 'Administrator',
                'contact_address_id'    => 0,
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