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
                'contact_mobile'        => '0',
                'contact_address_id'    => 1
            ];

        $profile['initials_avatar'] = json_encode($this->generateInitialsAvatar($profile));

        $profileAddress =
            [
                'address_type'        => 1,
                'is_primary'          => 1,
                'street_address'      => null,
                'street_address_2'    => null,
                'city_id'             => null,
                'city_name'           => null,
                'post_code'           => null,
                'state_id'            => null,
                'state_name'          => null,
                'country_id'          => null,
                'country_name'        => null,
                'package_name'        => 'profile',
                'package_row_id'      => 1
            ];

        if ($db) {
            $db->insertAsDict('basepackages_users_profiles', $profile);

            $db->insertAsDict('basepackages_address_book', $profileAddress);
        }

        if ($ff) {
            $profileStore = $ff->store('basepackages_users_profiles');

            $profileStore->updateOrInsert($profile);

            $addressStore = $ff->store('basepackages_address_book');

            $addressStore->updateOrInsert($profileAddress);
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