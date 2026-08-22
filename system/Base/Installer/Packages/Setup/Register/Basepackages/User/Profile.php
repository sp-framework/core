<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Register\Basepackages\User
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\User;

use LasseRafn\InitialAvatarGenerator\InitialAvatar;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Users\Profiles;
use Throwable;

/**
 * Seeds administrator user profile, contact details, initials avatar, and address book entry.
 */
class Profile
{
    /**
     * Registers default administrator profile, contact info, and address.
     *
     * @param mixed  $db       PDO database connection adapter.
     * @param mixed  $ff       FlatFile database manager.
     * @param string $country  Locale country ISO-3 code.
     * @param string $timezone Locale timezone identifier.
     *
     * @return void
     */
    public function register(mixed $db, mixed $ff, string $country, string $timezone): void
    {
        $profile = [
            'account_id'          => 1,
            'locale_country_iso3' => $country,
            'locale_timezone'     => $timezone,
            'settings'            => '[]'
        ];

        $profileContact = [
            'first_name'     => 'System',
            'last_name'      => 'Administrator',
            'full_name'      => 'System Administrator',
            'package_class'  => str_replace('\\', '_', Profiles::class),
            'package_row_id' => 1
        ];

        $profileContact['initials_avatar'] = json_encode($this->generateInitialsAvatar($profileContact));

        $profileAddress = [
            'address_reference' => 'Main',
            'street_address'    => null,
            'street_address_2'  => null,
            'street_address_3'  => null,
            'street_address_4'  => null,
            'city_id'           => null,
            'city_name'         => null,
            'post_code'         => null,
            'state_id'          => null,
            'state_name'        => null,
            'country_id'        => null,
            'country_name'      => null,
            'package_class'     => str_replace('\\', '_', Profiles::class),
            'package_row_id'    => 1
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

    /**
     * Generates initials avatar image array for contact book.
     *
     * @param array<string, mixed> $profileContact Contact details array.
     *
     * @return array<string, mixed> Avatar metadata structure.
     */
    protected function generateInitialsAvatar(array $profileContact): array
    {
        $avatars = ['small' => '', 'large' => ''];

        if (class_exists(InitialAvatar::class)) {
            try {
                $avatar = new InitialAvatar();

                $fullName = $profileContact['full_name'] ?? 'Admin User';
                $avatars['small'] = base64_encode($avatar->name($fullName)->autoColor()->height(30)->width(30)->generate()->stream('png', 100)->__toString());
                $avatars['large'] = base64_encode($avatar->name($fullName)->autoColor()->height(200)->width(200)->generate()->stream('png', 100)->__toString());
            } catch (Throwable $e) {
                // Return default empty array
            }
        }

        return $avatars;
    }
}