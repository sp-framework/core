<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\User;

use LasseRafn\InitialAvatarGenerator\InitialAvatar;

class Profile
{
    public function register($db, $ff)
    {
        $profile =
            [
                'account_id'                    => 1,
                'locale_country_id'             => 0,
                'locale_timezone_id'            => 0,
                'settings'                      => '[]'
            ];

        $profileContact =
            [
                'first_name'            => 'System',
                'last_name'             => 'Administrator',
                'full_name'             => 'System Administrator',
                'package_name'          => 'UsersProfiles',
                'package_row_id'        => 1
            ];

        $profileContact['initials_avatar'] = json_encode($this->generateInitialsAvatar($profileContact));

        $profileAddress =
            [
                'address_reference'     => 'Main',
                'street_address'        => null,
                'street_address_2'      => null,
                'street_address_3'      => null,
                'street_address_4'      => null,
                'city_id'               => null,
                'city_name'             => null,
                'post_code'             => null,
                'state_id'              => null,
                'state_name'            => null,
                'country_id'            => null,
                'country_name'          => null,
                'package_name'          => 'UsersProfiles',
                'package_row_id'        => 1
            ];

        if ($db) {
            $db->insertAsDict('basepackages_users_profiles', $profile);

            $db->insertAsDict('basepackages_contact_book', $profileContact);

            $db->insertAsDict('basepackages_address_book', $profileAddress);
        }

        if ($ff) {
            $profileStore = $ff->store('basepackages_users_profiles');

            $profileStore->updateOrInsert($profile);

            $contactStore = $ff->store('basepackages_contact_book');

            $contactStore->updateOrInsert($profileContact);

            $addressStore = $ff->store('basepackages_address_book');

            $addressStore->updateOrInsert($profileAddress);
        }
    }

    protected function generateInitialsAvatar($profileContact)
    {
        $avatar = new InitialAvatar();

        $avatars['small'] = base64_encode($avatar->name($profileContact['full_name'])->autoColor()->height(30)->width(30)->generate()->stream('png', 100));
        $avatars['large'] = base64_encode($avatar->name($profileContact['full_name'])->autoColor()->height(200)->width(200)->generate()->stream('png', 100));

        return $avatars;
    }
}