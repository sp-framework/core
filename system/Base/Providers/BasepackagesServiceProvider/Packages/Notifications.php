<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesNotifications;

class Notifications extends BasePackage
{
    protected $modelToUse = BasepackagesNotifications::class;

    public $notifications;

    protected function setNotificationsTypes()
    {
        return
            [
                [
                    'id'    => '0',
                    'name'  => 'system'
                ],
                [
                    'id'    => '1',
                    'name'  => 'email'
                ],
                [
                    'id'    => '2',
                    'name'  => 'messages'
                ],
            ];
    }

    public function addNotification(
        $notificationTitle,
        $notificationDetails = null,
        $appId = null,
        $accountId = null,
        $createdBy = 0,
        $packageName = null,
        $packageRowId = null,
        $notificationType = 0
    ) {
        if (!$notificationTitle) {
            throw new \Exception('Notification title missing');
        }

        $newNotification = [];
        $newNotification['notificationType'] = $notificationType;
        $newNotification['app_id'] = $appId;
        $newNotification['account_id'] = $accountId;
        $newNotification['created_by'] = $createdBy;
        $newNotification['package_name'] = $packageName;
        $newNotification['package_row_id'] = $packageRowId;
        $newNotification['notification_title'] = $notificationTitle;
        $newNotification['notification_details'] = $notificationDetails;

        if ($this->add($newNotification)) {
            $this->addResponse('Added new notification');
        } else {
            $this->addResponse('Error adding new notification', 1);
        }
    }

    public function fetchNewNotificationsCount($type = 0)
    {
        if (isset($this->auth->account()['profile']['settings']['notifications']['mute'])) {
            if ($this->auth->account()['profile']['settings']['notifications']['mute'] == true) {
                $isMute = true;
            } else {
                $isMute = false;
            }
        } else {
            $isMute = false;
        }

        $notifications = $this->getNotificationsCount($type);

        if ($notifications && is_array($notifications)) {
            $notificationsCount = count($notifications);
        } else {
            $notificationsCount = 0;
        }

        $this->addResponse('Ok', 0, ['count' => $notificationsCount, 'mute' => $isMute]);
    }

    protected function getNotificationsCount($type = 0)
    {
        return $this->getByParams(
                [
                    'conditions'    =>
                        '[notification_type] = :type: AND app_id = :appId: AND account_id = :aId: AND read = :read: AND archive = :archive:',
                    'bind'          =>
                        [
                            'type'          => $type,
                            'appId'         => $this->apps->getAppInfo()['id'],
                            'aId'           => $this->auth->account()['id'],
                            'read'          => 0,
                            'archive'       => 0
                        ]
                ]
            );
    }

    public function changeNotificationState(array $data)
    {
        $profile = $this->basepackages->profile->profile();

        if (isset($profile['settings']['notifications']['mute'])) {
            if ($data['changestate'] == 1) {
                $profile['settings']['notifications']['mute'] = true;
            } else if ($data['changestate'] == 0) {
                $profile['settings']['notifications']['mute'] = false;
            }
        } else {
            if ($data['changestate'] == 1) {
                $profile['settings']['notifications']['mute'] = true;
            } else if ($data['changestate'] == 0) {
                $profile['settings']['notifications']['mute'] = false;
            }
        }

        $profile['settings'] = Json::encode($profile['settings']);

        $this->basepackages->profile->updateProfile($profile);

        $this->addResponse('Changed');
    }

    public function getNotifications($type = 0)
    {
        $notificationsArr = $this->paged(
            [
                'conditions'    =>
                    '[notification_type] = :type: AND app_id = :appId: AND account_id = :aId: AND read = :read: AND archive = :archive:',
                'bind'          =>
                    [
                        'type'          => $type,
                        'appId'         => $this->apps->getAppInfo()['id'],
                        'aId'           => $this->auth->account()['id'],
                        'read'          => 0,
                        'archive'       => 0
                    ]
            ]
        );

        if ($notificationsArr && count($notificationsArr) > 0) {
            foreach ($notificationsArr as $key => &$notification) {
                $notification['notification'] = Json::decode($notification['notification'], true);
            }
        }

        return $notificationsArr;
    }

    public function markRead(array $data)
    {
        $notification = $this->getByParams(
            [
                'conditions'    =>
                    'id = :id: AND account_id = :aId: AND read = :read:',
                'bind'          =>
                    [
                        'id'            => $data['id'],
                        'aId'           => $this->auth->account()['id'],
                        'read'          => 0,
                    ]
            ]
        );

        if (count($notification) === 1) {
            $notification[0]['read'] = 1;

            $this->update($notification[0]);

            $this->addResponse('Ok');

            return;
        }

        $this->addResponse('Id Not Found', 1);
    }

    public function markArchive(array $data)
    {
        $notification = $this->getByParams(
            [
                'conditions'    =>
                    'id = :id: AND account_id = :aId: AND archive = :archive:',
                'bind'          =>
                    [
                        'id'            => $data['id'],
                        'aId'           => $this->auth->account()['id'],
                        'archive'       => 0,
                    ]
            ]
        );

        if (count($notification) === 1) {
            $notification[0]['read'] = 1;
            $notification[0]['archive'] = 1;

            $this->update($notification[0]);

            $this->addResponse('Ok');

            return;
        }

        $this->addResponse('Id Not Found', 1);
    }

    public function removeNotification(array $data)
    {
        $notification = $this->getByParams(
            [
                'conditions'    =>
                    'id = :id: AND account_id = :aId:',
                'bind'          =>
                    [
                        'id'            => $data['id'],
                        'aId'           => $this->auth->account()['id']
                    ]
            ]
        );

        if (count($notification) === 1) {
            $this->remove($notification[0]['id']);

            $this->addResponse('Ok');

            return;
        }

        $this->addResponse('Id Not Found', 1);
    }
}