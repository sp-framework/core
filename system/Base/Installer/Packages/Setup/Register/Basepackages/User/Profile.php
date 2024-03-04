<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\User;

class Profile
{
    public function register($db, $ff)
    {
        $profile =
            [
                'account_id'            => 1,
                'first_name'            => 'System',
                'last_name'             => 'Administrator',
                'full_name'             => 'System Administrator',
                'contact_phone'         => '0',
                'contact_mobile'        => '0'
            ];

        if ($db) {
            $db->insertAsDict('basepackages_users_profiles', $profile);
        }

        if ($ff) {
            $profileStore = $ff->store('basepackages_users_profiles');

            $profileStore->updateOrInsert($profile);
        }
    }
}