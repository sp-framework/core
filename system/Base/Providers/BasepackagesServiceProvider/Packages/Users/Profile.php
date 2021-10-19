<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Users;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsAgents;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsIdentifiers;
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

    protected $messengerSettings;

    public $profile;

    public function profile(int $accountId = null)
    {
        if (!$accountId) {
            $accountId = $this->auth->account()['id'];
        }

        if (!$this->profile) {
            $this->profile = $this->getProfile($accountId);
        }

        return $this->profile;
    }

    public function getProfile(int $accountId)
    {
        $profileObj = $this->getFirst('account_id', $accountId);

        if ($profileObj) {
            $profile = $profileObj->toArray();

            if ($profile['settings'] &&
                !is_array($profile['settings']) &&
                $profile['settings'] !== ''
            ) {
                $profile['settings'] = Json::decode($profile['settings'], true);
            } else {
                $profile['settings'] = [];
            }

            $addressObj = $profileObj->getAddress();

            $profile['address'] = [];

            if ($addressObj) {
                $profile['address'] = $addressObj->toArray();
            }

            $profile['role'] = $this->basepackages->roles->getById($this->auth->account()['role_id'])['name'];

            return $profile;
        }
    }

    /**
     * @notification(name=add)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
    public function addProfile(array $data)
    {
        $accountId = $data['id'];
        unset($data['id']);

        $data['account_id'] = $accountId;
        $data['full_name'] = $data['first_name'] . ' ' . $data['last_name'];
        $data['contact_phone'] = '0';
        $data['contact_mobile'] = '0';

        if ($this->add($data)) {
            $this->addResponse('Profile added');
        } else {
            $this->addResponse('Error adding profile.', 1);
        }
    }

    /**
     * @notification(name=update)
     * notification_allowed_methods(email, sms)//Example
     * @notification_allowed_methods(email, sms)
     */
    public function updateProfileViaAccount(array $data)
    {
        $profile = $this->getProfile($data['id']);

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
            $profile['settings'] = Json::encode($profile['settings']);
        }

        if ($this->update($profile)) {
            $this->addResponse('Profile updated');
        } else {
            $this->addResponse('Error updating profile.', 1);
        }
    }

    public function updateProfile(array $data)
    {
        if (!$this->auth->account()) {
            return;
        }

        if (isset($data['subscriptions']) && $data['subscriptions'] !== '') {
            $data['subscriptions'] = Json::decode($data['subscriptions'], true);
            $this->modules->packages->updateNotificationSubscriptions($data['subscriptions']);
            unset($data['subscriptions']);
        }

        $profile = $this->getProfile($this->auth->account()['id']);

        $profile = array_merge($profile, $data);

        $profile['full_name'] = $profile['first_name'] . ' ' . $profile['last_name'];

        if (isset($profile['address']['id']) &&
             $profile['address']['id'] != 0 &&
             $profile['address']['id'] !== ''
        ) {
            $address = $profile;

            $address['package_name'] = $this->packageName;

            $address['package_row_id'] = $profile['id'];

            $address['address_id'] = $profile['address']['id'];

            $this->basepackages->addressbook->mergeAndUpdate($address);

        } else if (!$profile['address']['id'] ||
                    $profile['address']['id'] == 0 ||
                    $profile['address']['id'] === ''
        ) {
            $address = $profile;

            $address['package_name'] = $this->packageName;

            $address['package_row_id'] = $profile['id'];

            $this->basepackages->addressbook->addAddress($address);
        }

        $portrait = $this->getProfile($this->auth->account()['id'])['portrait'];

        if (is_array($profile['settings'])) {
            $profile['settings'] = Json::encode($profile['settings']);
        }

        if ($this->update($profile)) {
            $this->basepackages->storages->changeOrphanStatus($data['portrait'], $portrait);

            $this->addResponse('Profile updated');
        } else {
            $this->addResponse('Error updating profile.', 1);
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

    public function generateViewData()
    {
        $accountObj = $this->basepackages->accounts->getFirst('id', $this->auth->account()['id']);

        $canLoginArr = $accountObj->canlogin->toArray();

        $account = $accountObj->toArray();

        if ($canLoginArr > 0) {
            foreach ($canLoginArr as $key => $value) {
                $account['can_login'][$value['app']] = $value['allowed'];
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

        $notifications = [];

        $appsArr = $this->apps->apps;

        foreach ($appsArr as $appKey => $app) {
            if (isset($account['can_login'][$app['route']])) {
                $packagesArr = $this->modules->packages->getPackagesForApp($app['id']);

                if (count($packagesArr) > 0) {
                    $packages[$app['id']] =
                        [
                            'title' => strtoupper($app['name']),
                            'id' => strtoupper($app['id'])
                        ];

                    foreach ($packagesArr as $key => $package) {
                        if ($package['class']) {
                            $reflector = $this->annotations->get($package['class']);
                            $methods = $reflector->getMethodsAnnotations();

                            if ($methods) {
                                $packages[$app['id']]['childs'][$key]['id'] = $package['id'];
                                $packages[$app['id']]['childs'][$key]['title'] = $package['display_name'];
                            }
                        }
                    }

                    if (!isset($packages[$app['id']]['childs'])) {
                        unset($packages[$app['id']]);
                    }
                }
            }
        }

        $this->packagesData->packages = $packages;

        $notifications = [];

        foreach ($appsArr as $appKey => $app) {
            if (isset($account['can_login'][$app['route']])) {

                $packagesArr = $this->modules->packages->getPackagesForApp($app['id']);

                foreach ($packagesArr as $key => $package) {
                    if ($package['class'] && $package['class'] !== '') {
                        if ($package['notification_subscriptions'] &&
                            !is_array($package['notification_subscriptions']) &&
                            $package['notification_subscriptions'] !== ''
                        ) {
                            $package['notification_subscriptions'] = Json::decode($package['notification_subscriptions'], true);
                        }

                        $reflector = $this->annotations->get($package['class']);
                        $methods = $reflector->getMethodsAnnotations();

                        if ($methods) {
                            foreach ($methods as $annotation) {
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

                                if (isset($package['notification_subscriptions'][$app['id']])) {
                                    foreach ($thisSubscriptions as $subscriptionKey => $subscriptionValue) {
                                        if (isset($package['notification_subscriptions'][$app['id']][$subscriptionValue])) {

                                            if ($subscriptionValue === 'email' || $subscriptionValue === 'sms') {
                                                if (isset($package['notification_subscriptions'][$app['id']][$subscriptionValue][$account['id']])) {
                                                    $notifications[$app['id']][$package['id']][$subscriptionValue] = 1;
                                                } else {
                                                    $notifications[$app['id']][$package['id']][$subscriptionValue] = 0;
                                                }
                                            } else {
                                                if (in_array($account['id'], $package['notification_subscriptions'][$app['id']][$subscriptionValue])) {
                                                    $notifications[$app['id']][$package['id']][$subscriptionValue] = 1;
                                                } else {
                                                    $notifications[$app['id']][$package['id']][$subscriptionValue] = 0;
                                                }
                                            }
                                        } else {
                                            $notifications[$app['id']][$package['id']][$subscriptionValue] = 0;
                                        }
                                    }
                                } else {
                                    foreach ($thisSubscriptions as $subscriptionKey => $subscriptionValue) {
                                        $notifications[$app['id']][$package['id']][$subscriptionValue] = 0;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->packagesData->subscriptions = Json::encode($subscriptions);

        $this->packagesData->notifications = Json::encode($notifications);

        //We grab agents instead of sessions so we can clear stale agents as well as sessions. Number of agents can be more then sessions and each agent will have its session ID.
        $this->packagesData->sessions = [];

        $agentsModel = new BasepackagesUsersAccountsAgents;

        $agentsObj = $agentsModel->findByaccount_id($this->auth->account()['id']);

        if ($agentsObj) {
            $agents = $agentsObj->toArray();

            if (count($agents) > 0) {
                $identifiersModel = new BasepackagesUsersAccountsIdentifiers;

                foreach ($agents as &$agent) {
                    if ($identifiersModel::findBysession_id($agent['session_id'])) {
                        $agent['remember'] = true;
                    }
                }
            }

            $this->packagesData->sessions = $agents;
        }

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
}