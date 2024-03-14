<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\User;

use LasseRafn\InitialAvatarGenerator\InitialAvatar;

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

        $profile['initials_avatar'] = json_encode($this->generateInitialsAvatar($profile));

        if ($db) {
            $db->insertAsDict('basepackages_users_profiles', $profile);
        }

        if ($ff) {
            $profileStore = $ff->store('basepackages_users_profiles');

            $profileStore->updateOrInsert($profile);
        }
    }

    protected function generateInitialsAvatar($profile)
    {
        $avatar = new InitialAvatar();

        $avatars['small'] = base64_encode($avatar->name($profile['full_name'])->autoColor()->height(30)->width(30)->generate()->stream('png', 100));
        $avatars['large'] = base64_encode($avatar->name($profile['full_name'])->autoColor()->height(200)->width(200)->generate()->stream('png', 100));

        return $avatars;
    }
}