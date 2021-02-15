<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Users;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersProfiles;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Users\Roles;

class Profile extends BasePackage
{
    protected $modelToUse = BasepackagesUsersProfiles::class;

    protected $packageName = 'profile';

    protected $maleAvatarDir;

    protected $femaleAvatarDir;

    protected $avatarProperties = [];

    protected $avatar;

    public $profile;

    public function profile(int $accountId)
    {
        if (!$this->profile) {
            $this->getProfile($accountId);
        }

        return $this->profile;
    }

    protected function getProfile(int $accountId)
    {
        $profile =
            $this->getByParams(
            [
                'conditions'    => 'account_id = :aid:',
                'bind'          =>
                    [
                        'aid'   => $accountId
                    ]
            ]
        );

        if (count($profile) === 1) {
            $profile = $profile[0];

            $profile['role'] =
                $this->basepackages->roles->getById($this->auth->account()['role_id'])['name'];

            if ($profile['contact_address_id'] && $profile['contact_address_id'] !== '') {
                $address = $this->basepackages->addressbook->getById($profile['contact_address_id']);

                unset($address['id']);

                $profile = array_merge($profile, $address);
            } else {
                $profile['street_address'] = '';
                $profile['street_address_2'] = '';
                $profile['city_id'] = '';
                $profile['city_name'] = '';
                $profile['post_code'] = '';
                $profile['state_id'] = '';
                $profile['state_name'] = '';
                $profile['country_id'] = '';
                $profile['country_name'] = '';
            }
        }

        $this->profile = $profile;
    }

    public function updateProfile(array $data)
    {
        if (!$this->auth->account()) {
            return;
        }

        $profile = $this->getById($this->auth->account()['id']);

        $profile = array_merge($profile, $data);

        $profile['full_name'] = $profile['first_name'] . ' ' . $profile['last_name'];

        if ($profile['contact_address_id'] &&
             $profile['contact_address_id'] != 0 &&
             $profile['contact_address_id'] !== ''
        ) {
            $address = $profile;

            $address['package_name'] = $this->packageName;

            $address['name'] = $profile['full_name'];

            $address['address_id'] = $profile['contact_address_id'];

            $this->basepackages->addressbook->mergeAndUpdate($address);

        } else if (!$profile['contact_address_id'] ||
                    $profile['contact_address_id'] == 0 ||
                    $profile['contact_address_id'] === ''
        ) {
            $address = $profile;

            $address['package_name'] = $this->packageName;

            $address['name'] = $profile['full_name'];

            $address['id'] = '';

            $this->basepackages->addressbook->addAddress($address);

            $profile['contact_address_id'] = $this->basepackages->addressbook->packagesData->last['id'];
        }

        $portrait = $this->getById($this->auth->account()['id'])['portrait'];

        if ($this->update($profile)) {
            $this->basepackages->storages->changeOrphanStatus($data['portrait'], $portrait);

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Profile updated';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating profile.';
        }
    }

    public function generateAvatar(string $regenerateUsingFile = null, string $gender = 'M')
    {
        $this->maleAvatarDir = 'public/dash/default/images/avatar/male/';

        $this->femaleAvatarDir = 'public/dash/default/images/avatar/female/';

        $avatarImageArr = [];

        if ($gender === 'M') {
            $avatarDir = $this->maleAvatarDir;
        } else if ($gender === 'F') {
            $avatarDir = $this->femaleAvatarDir;
        }

        //Sequence important
        $this->avatarProperties = ['background','face','hair','eyes','clothes','mouth'];

        if (!$regenerateUsingFile) {
            $counterFileNames = [];

            foreach ($this->avatarProperties as $avatarPropertyKey => $avatarPropertyValue) {
                $dirContents = $this->localContent->listContents($avatarDir.'/'.$avatarPropertyValue, true);

                $files = [];

                foreach ($dirContents as $content) {
                    if ($content['type'] === 'file') {
                        array_push($files, $content['basename']);
                    }
                }

                $fileName = $files[rand(0, count($files) - 1)];

                $counterFileNames[$avatarPropertyValue] = $fileName;

                $avatarImageArr[$avatarPropertyValue] =
                    imagecreatefrompng(
                        base_path($avatarDir . $avatarPropertyValue . '/' . $fileName)
                    );
            }
        } else {
            $regenerateUsingFile = str_replace('.png', '', $regenerateUsingFile);

            $fileArr = explode('_', $regenerateUsingFile);

            $gender = $fileArr[0];

            unset($fileArr[0]);

            foreach ($fileArr as $key => &$value) {
                $value = $value . '.png';
            }

            if ($gender === 'M') {
                $avatarDir = $this->maleAvatarDir;
            } else if ($gender === 'F') {
                $avatarDir = $this->femaleAvatarDir;
            }

            $counterFileNames = array_combine($this->avatarProperties, $fileArr);

            foreach ($counterFileNames as $type => $filename) {
                $avatarImageArr[$type] =
                    imagecreatefrompng(
                        base_path($avatarDir . $type . '/' . $filename)
                    );
            }

        }

        $this->avatar = $avatarImageArr['background'];

        array_shift($avatarImageArr);

        foreach ($avatarImageArr as $avatarImageKey => $avatarImageValue) {
            imagecopy($this->avatar, $avatarImageValue, 0, 0, 0, 0, 400, 400);
        }

        ob_start();
            imagepng($this->avatar);
            $avatar = ob_get_contents();
        ob_end_clean();

        $this->packagesData->avatar = base64_encode($avatar);

        foreach ($counterFileNames as $key => &$counterFileName) {
            $counterFileName = str_replace('.png', '', $counterFileName);
        }

        $this->packagesData->avatarName = $gender . '_' . implode('_', $counterFileNames) . '.png';

        return true;
    }
}