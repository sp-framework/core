<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Users;

use System\Base\BasePackage;
use LasseRafn\InitialAvatarGenerator\InitialAvatar;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Users\Roles;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersProfiles;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsAgents;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsIdentifiers;

class Profiles extends BasePackage
{
    protected $modelToUse = BasepackagesUsersProfiles::class;

    protected $packageName = 'profile';

    protected $maleAvatarDir;

    protected $femaleAvatarDir;

    protected $avatarProperties = [];

    protected $avatar;

    protected $messengerSettings;

    public $profile;

    public function profile(int $accountId = null)
    {
        if (!$accountId) {
            $accountId = $this->access->auth->account()['id'];
        }

        if (!$this->profile) {
            $this->profile = $this->getProfile($accountId);
        }

        return $this->profile;
    }

    public function getProfile($account)
    {
        if (is_array($account)) {
            $accountId = $account['id'];
            $accountRoleId = $account['role']['id'];
        } else {
            $accountId = $account;
        }

        if ($this->config->databasetype === 'db') {
            $profileObj = $this->getFirst('account_id', $accountId);

            if ($profileObj) {
                $profile = $profileObj->toArray();

                if ($profile['settings'] &&
                    !is_array($profile['settings']) &&
                    $profile['settings'] !== ''
                ) {
                    $profile['settings'] = $this->helper->decode($profile['settings'], true);
                } else {
                    $profile['settings'] = [];
                }

                $addressObj = $profileObj->getAddress();

                $profile['address'] = [];

                if ($addressObj) {
                    $profile['address'] = $addressObj->toArray();
                }

                if (isset($accountRoleId)) {
                    $profile['role'] = $this->basepackages->roles->getById($accountRoleId)['name'];
                }

                return $profile;
            }
        } else {
            $this->setFFRelations(true);
            $this->setFFRelationsConditions(['address' => ['package_name', '=', 'UsersProfiles']]);

            $profile = $this->getFirst('account_id', $accountId, false, true, null, [], true);

            if ($profile) {
                if (isset($accountRoleId)) {
                    $profile['role'] = $this->basepackages->roles->getById($accountRoleId)['name'];
                }

                return $profile;
            }
        }

        return false;
    }

    public function addProfile(array $data)
    {
        $accountId = $data['id'];
        unset($data['id']);

        $data['account_id'] = $accountId;
        $data['full_name'] = $data['first_name'] . ' ' . $data['last_name'];
        $data['initials_avatar'] = json_encode($this->generateInitialsAvatar($data));
        $data['contact_phone'] = '0';
        $data['contact_mobile'] = '0';

        if ($this->add($data)) {
            //To Update Address Book
            $profile = $this->packagesData->last;
            $profile['address_type']        = 1;
            $profile['is_primary']          = 1;
            $profile['street_address']      = null;
            $profile['street_address_2']    = null;
            $profile['city_id']             = null;
            $profile['city_name']           = '';
            $profile['post_code']           = null;
            $profile['state_id']            = null;
            $profile['state_name']          = '';
            $profile['country_id']          = null;
            $profile['country_name']        = '';
            $profile['package_name']        = 'UsersProfiles';
            $profile['package_row_id']      = $profile['id'];

            $profile['contact_address_id'] = $this->addProfileAddress($profile);

            $this->update($profile);

            $this->addResponse('Profile added');
        } else {
            $this->addResponse('Error adding profile.', 1);
        }
    }

    public function updateProfileViaAccount(array $data)
    {
        $profile = $this->getProfile($data['profile_package_row_id']);

        if (isset($data['first_name']) && isset($data['last_name'])) {
            if (($data['first_name'] !== $profile['first_name'] ||
                $data['last_name'] !== $profile['last_name']) ||
                !$profile['initials_avatar']
            ) {
                $data['initials_avatar'] = json_encode($this->generateInitialsAvatar($data));
            }
        }

        unset($data['id']);

        $profile = array_merge($profile, $data);

        if (isset($data['first_name']) && isset($data['last_name'])) {
            $profile['full_name'] = $data['first_name'] . ' ' . $data['last_name'];
        }

        if ($profile['contact_phone'] === '') {
            $profile['contact_phone'] = 0;
        }
        if ($profile['contact_mobile'] === '') {
            $profile['contact_mobile'] = 0;
        }

        if (is_array($profile['settings'])) {
            $profile['settings'] = $this->helper->encode($profile['settings']);
        }

        if ($this->update($profile)) {
            $this->addResponse('Profile updated');
        } else {
            $this->addResponse('Error updating profile.', 1);
        }
    }

    public function updateProfile(array $data)
    {
        if (!isset($data['account_id']) && !$this->access->auth->account()) {
            return;
        }

        if (isset($data['subscriptions']) && $data['subscriptions'] !== '') {
            $data['subscriptions'] = $this->helper->decode($data['subscriptions'], true);
            $this->modules->components->updateNotificationSubscriptions($data['subscriptions']);
            $this->modules->packages->updateNotificationSubscriptions($data['subscriptions']);
            unset($data['subscriptions']);
        }

        if (isset($data['account_id'])) {
            $profile = $this->getProfile((int) $data['account_id']);
        } else {
            $profile = $this->getProfile($this->access->auth->account()['id']);
        }

        if (($data['first_name'] !== $profile['first_name'] ||
            $data['last_name'] !== $profile['last_name']) ||
            !$profile['initials_avatar']
        ) {
            $data['initials_avatar'] = json_encode($this->generateInitialsAvatar($data));
        }

        $profile = array_merge($profile, $data);

        $profile['full_name'] = $profile['first_name'] . ' ' . $profile['last_name'];

        if ($profile['contact_address_id']) {
            $address = $profile;

            $address['package_name'] = 'UsersProfiles';

            $address['package_row_id'] = $profile['id'];

            $this->basepackages->addressbook->mergeAndUpdate($address);
        } else {
            $profile['contact_address_id'] = $this->addProfileAddress($profile);
        }

        $portrait = $this->getProfile($this->access->auth->account()['id'])['portrait'];

        if (is_array($profile['settings'])) {
            $profile['settings'] = $this->helper->encode($profile['settings']);
        }

        if ($this->update($profile)) {
            $this->basepackages->storages->changeOrphanStatus($data['portrait'], $portrait);

            $this->addResponse('Profile updated');
        } else {
            $this->addResponse('Error updating profile.', 1);
        }
    }

    protected function addProfileAddress($profile)
    {
        $address = $profile;

        $address['package_name'] = $this->packageName;

        $address['package_row_id'] = $profile['id'];

        if (isset($address['id'])) {
            unset($address['id']);
        }

        $profileAddress = $this->basepackages->addressbook->addAddress($address);

        if ($profileAddress) {
            return $profileAddress['id'];
        }

        return null;
    }

    public function generateAvatar(string $regenerateUsingFile = null, string $gender = 'M')
    {
        $this->maleAvatarDir = 'public/core/default/images/avatar/male/';

        $this->femaleAvatarDir = 'public/core/default/images/avatar/female/';

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
                $dirContents = $this->localContent->listContents($avatarDir.$avatarPropertyValue, true);

                $files = [];
                foreach ($dirContents as $content) {
                    if ($content instanceof \League\Flysystem\FileAttributes) {
                        array_push($files, base_path($content->path()));
                    }
                }

                $fileName = $files[rand(0, count($files) - 1)];

                $counterFileNames[$avatarPropertyValue] = $fileName;

                $avatarImageArr[$avatarPropertyValue] = imagecreatefrompng($fileName);
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
                try {
                    $avatarImageArr[$type] =
                        imagecreatefrompng(
                            base_path($avatarDir . $type . '/' . $filename)
                        );
                } catch (\Exception $e) {
                    return false;
                }
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

        foreach ($counterFileNames as $type => &$counterFileName) {
            $counterFileName = str_replace('.png', '', str_replace(base_path($avatarDir . $type . '/'), '', $counterFileName));
        }

        $this->packagesData->avatarName = $gender . '_' . implode('_', $counterFileNames) . '.png';

        return true;
    }

    public function generateViewData($accountId = null)
    {
        if ($accountId) {
            $this->packagesData->account = $this->basepackages->accounts->getAccountById($accountId);
        } else {
            $this->packagesData->account = $this->access->auth->account();
        }

        $this->packagesData->profile = $this->getProfile($this->packagesData->account);

        if ($this->config->databasetype === 'db') {
            $accountObj = $this->basepackages->accounts->getFirst('id', $this->access->auth->account()['id']);

            $canLoginArr = $accountObj->canlogin->toArray();

            $account = $accountObj->toArray();
        } else {
            $account = $this->basepackages->accounts->getAccountById($this->access->auth->account()['id']);

            $canLoginArr = $account['canlogin'];
        }

        if ($canLoginArr > 0) {
            foreach ($canLoginArr as $key => $value) {
                $account['can_login'][$value['app_id']] = $value['allowed'];
            }
        } else {
            $account['can_login'] = [];
        }

        if (isset($this->domains->getDomain()['apps'][$this->app['id']]['email_service']) &&
            $this->domains->getDomain()['apps'][$this->app['id']]['email_service'] !== '' &&
            $this->domains->getDomain()['apps'][$this->app['id']]['email_service'] !== 0
        ) {
            $this->packagesData->canEmail = true;
        } else {
            $this->packagesData->canEmail = false;
        }

        $notifications_modules = [];
        $notifications = [];
        $subscriptions = [
            'add' => 'add',
            'update' => 'update',
            'remove' => 'remove'
        ];

        $appsArr = $this->apps->apps;

        foreach ($appsArr as $appKey => $app) {
            if (isset($account['can_login'][$app['id']])) {
                $notifications_modules[$app['id']] =
                    [
                        'title' => strtoupper($app['name']),
                        'id' => strtoupper($app['id'])
                    ];

                $allModules['components'] = msort($this->modules->components->getComponentsForAppId($app['id']), 'name');
                $allModules['packages'] = msort($this->modules->packages->getPackagesForAppId($app['id']), 'display_name');

                foreach ($allModules as $moduleType => $modules) {
                    if ($modules && count($modules) > 0) {
                        foreach ($modules as $moduleKey => $module) {
                            if (!isset($notifications_modules[$app['id']]['childs'][$moduleType])) {
                                $notifications_modules[$app['id']]['childs'][$moduleType]['title'] = strtoupper($moduleType);
                            }

                            if ($module['notification_subscriptions'] &&
                                !is_array($module['notification_subscriptions']) &&
                                $module['notification_subscriptions'] !== ''
                            ) {
                                $module['notification_subscriptions'] = $this->helper->decode($module['notification_subscriptions'], true);
                            }

                            $reflector = $this->annotations->get($module['class']);
                            $methods = $reflector->getMethodsAnnotations();

                            if ($methods && count($methods) > 0) {
                                foreach ($methods as $annotation) {
                                    if ($annotation->getAll('notification')) {
                                        $notifications_modules[$app['id']]['childs'][$moduleType]['childs'][$moduleKey]['id'] = $module['id'];
                                        if ($moduleType === 'packages') {
                                            $notifications_modules[$app['id']]['childs'][$moduleType]['childs'][$moduleKey]['title'] = strtoupper($module['display_name']);
                                        } else {
                                            $notifications_modules[$app['id']]['childs'][$moduleType]['childs'][$moduleKey]['title'] = strtoupper($module['name']);
                                        }

                                        $thisSubscriptions = [];
                                        $notification_action = $annotation->getAll('notification')[0]->getArguments();
                                        $notification_allowed_methods = $annotation->getAll('notification_allowed_methods');

                                        if (count($notification_allowed_methods) > 0) {
                                            $notification_allowed_methods = $annotation->getAll('notification_allowed_methods')[0]->getArguments();
                                        }

                                        $subscriptions[$notification_action['name']] = $notification_action['name'];
                                        $thisSubscriptions[$notification_action['name']] = $notification_action['name'];

                                        if (count($notification_allowed_methods) > 0) {
                                            foreach ($notification_allowed_methods as $allowedMethodKey => $allowedMethod) {
                                                $subscriptions[$allowedMethod] = $allowedMethod;
                                                $thisSubscriptions[$allowedMethod] = $allowedMethod;
                                            }
                                        }

                                        if (isset($module['notification_subscriptions'][$app['id']])) {
                                            foreach ($thisSubscriptions as $subscriptionKey => $subscriptionValue) {
                                                if (isset($module['notification_subscriptions'][$app['id']][$subscriptionValue])) {

                                                    if ($subscriptionValue === 'email') {
                                                        if (isset($module['notification_subscriptions'][$app['id']][$subscriptionValue][$account['id']])) {
                                                            $notifications[$app['id']][$moduleType][$module['id']][$subscriptionValue] = 1;
                                                        } else {
                                                            $notifications[$app['id']][$moduleType][$module['id']][$subscriptionValue] = 0;
                                                        }
                                                    } else {
                                                        if (in_array($account['id'], $module['notification_subscriptions'][$app['id']][$subscriptionValue])) {
                                                            $notifications[$app['id']][$moduleType][$module['id']][$subscriptionValue] = 1;
                                                        } else {
                                                            $notifications[$app['id']][$moduleType][$module['id']][$subscriptionValue] = 0;
                                                        }
                                                    }
                                                } else {
                                                    $notifications[$app['id']][$moduleType][$module['id']][$subscriptionValue] = 0;
                                                }
                                            }
                                        } else {
                                            foreach ($thisSubscriptions as $subscriptionKey => $subscriptionValue) {
                                                $notifications[$app['id']][$moduleType][$module['id']][$subscriptionValue] = 0;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if (!isset($notifications_modules[$app['id']]['childs'])) {
                    unset($notifications_modules[$app['id']]);
                }
            }
        }

        $this->packagesData->notifications_modules = $notifications_modules;

        $this->packagesData->subscriptions = $this->helper->encode($subscriptions);

        $this->packagesData->notifications = $this->helper->encode($notifications);

        //We grab agents instead of sessions so we can clear stale agents as well as sessions. Number of agents can be more then sessions and each agent will have its session ID.
        $this->packagesData->sessions = [];

        $agentsModel = new BasepackagesUsersAccountsAgents;
        $identifiersModel = new BasepackagesUsersAccountsIdentifiers;

        if ($this->config->databasetype === 'db') {
            $agentsObj = $agentsModel->findByaccount_id($this->access->auth->account()['id']);

            if ($agentsObj) {
                $agents = $agentsObj->toArray();

                if (count($agents) > 0) {
                    foreach ($agents as &$agent) {
                        if ($identifiersModel::findBysession_id($agent['session_id'])) {
                            $agent['remember'] = true;
                        }
                    }
                }

            }
        } else {
            $agentsStore = $this->ff->store($agentsModel->getSource());

            $agents = $agentsStore->findAll();

            if (count($agents) > 0) {
                $identifiersStore = $this->ff->store($identifiersModel->getSource());

                foreach ($agents as &$agent) {
                    if ($identifiersStore->findOneBy(['session_id', '=', $agent['session_id']])) {
                        $agent['remember'] = true;
                    }
                }
            }
        }

        $this->packagesData->sessions = $agents;

        $this->packagesData->coreSettings = $this->core->core['settings'];

        $this->packagesData->canUse2fa = $this->access->auth->twoFa->canUse2fa();

        return true;
    }

    public function getMessengerSettings()
    {
        if ($this->messengerSettings) {
            return $this->messengerSettings;
        }

        if (isset($this->profile['settings']['messenger'])) {
            $messengerSettings = $this->profile['settings']['messenger'];

            if (is_array($messengerSettings)) {
                if (isset($messengerSettings['members']['users']) &&
                    is_array($messengerSettings['members']['users']) &&
                    count($messengerSettings['members']['users']) > 0
                ) {
                    foreach ($messengerSettings['members']['users'] as $accountKey => $accountId) {
                        $account = $this->basepackages->accounts->getById($accountId);

                        if ($account) {
                            $messengerSettings['members']['users'][$accountKey] = [];
                            $messengerSettings['members']['users'][$accountKey]['id'] = $account['id'];
                            $messengerSettings['members']['users'][$accountKey]['email'] = $account['email'];
                            $profile = $this->getProfile($account['id']);

                            if ($profile) {
                                $messengerSettings['members']['users'][$accountKey]['full_name'] = $profile['full_name'];
                                if ($profile['portrait'] !== '') {
                                    $messengerSettings['members']['users'][$accountKey]['portrait'] = $profile['portrait'];
                                }
                                if (isset($profile['settings']['messenger']) && isset($profile['settings']['messenger']['status'])) {
                                    $messengerSettings['members']['users'][$accountKey]['status'] = $profile['settings']['messenger']['status'];
                                } else {
                                    $messengerSettings['members']['users'][$accountKey]['status'] = 4;
                                }
                            }
                        }
                    }
                } else {
                    $messengerSettings['members']['users'] = null;
                }
            }

            $this->messengerSettings = $messengerSettings;

            return $messengerSettings;
        }

        return null;
    }

    public function addUserToMembersUsers(array $data)
    {
        if (isset($this->profile['settings']['messenger']['members']['users'])) {
            if (!in_array($data['user']['id'], $this->profile['settings']['messenger']['members']['users'])) {
                array_push($this->profile['settings']['messenger']['members']['users'], $data['user']['id']);
            } else {
                $this->addResponse('User already added');

                return;
            }
        } else {
            $this->profile['settings']['messenger']['members']['users'][] = $data['user']['id'];
        }

        if ($this->update($this->profile)) {
            $this->addResponse('Added to members users');

            return;
        }

        $this->addResponse('Could not add to members users', 1);
    }

    protected function generateInitialsAvatar($profileData)
    {
        $avatar = new InitialAvatar();

        $avatars['small'] = base64_encode($avatar->name($profileData['first_name'] . ' ' . $profileData['last_name'])->autoColor()->height(30)->width(30)->generate()->stream('png', 100));
        $avatars['large'] = base64_encode($avatar->name($profileData['first_name'] . ' ' . $profileData['last_name'])->autoColor()->height(200)->width(200)->generate()->stream('png', 100));

        return $avatars;
    }
}