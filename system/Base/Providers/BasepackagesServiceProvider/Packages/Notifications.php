<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesNotifications;
use ZMQContext;

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
            $this->pushNotification($newNotification);

            $this->addResponse('Added new notification');
        } else {
            $this->addResponse('Error adding new notification', 1);
        }
    }

    protected function pushNotification($notification)
    {
        $account = $this->basepackages->accounts->getById($notification['account_id']);

        if ($account['notifications_tunnel_id']) {
            $count =
                $this->modelToUse::count(
                    [
                        'conditions' => 'account_id = :aid: AND read = :r:',
                        'bind'  => [
                            'aid' => $notification['account_id'],
                            'r' => 0
                        ]
                    ]
            );

            if ($count && $count > 0) {
                $context = new ZMQContext();

                $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'New Notification');
                $socket->connect("tcp://localhost:5555");

                $data =
                    [
                        'type'              => 'systemNotifications',
                        'to'                => $account['notifications_tunnel_id'],
                        'response'          => [
                            'responseCode'      => 0,
                            'responseData'      =>
                                [
                                    "count" => $count
                                ]
                        ]
                    ];

                $socket->send(Json::encode($data));
            }
        }
    }

    public function emailNotification(
        $emailAddresses,
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

        $body = 'Notification Title: ' . $notificationTitle . '<br>Notification Details: ' . $notificationDetails . '<br>';

        if ($createdBy != 0) {
            $profile = $this->basepackages->profile->getProfile($createdBy);

            $now = date("F j, Y, g:i a");

            if ($profile) {
                $body .= 'Notification By: ' . $profile['full_name'] . ' (' . $now . ')<br>';
            } else {
                $body .= 'Notification By: System (' . $now . ')<br>';
            }
        } else {
            $body .= 'Notification By: System (' . $now . ')<br>';
        }

        $email['app_id'] = $appId;
        $email['status'] = 1;
        $email['priority'] = 3;
        $email['to_addresses'] = Json::encode($emailAddresses);
        $email['subject'] = 'Notification for ' . $this->domains->getDomain()['name'];
        $email['body'] = $body;

        $this->basepackages->emailqueue->addToQueue($email);
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

    public function bulk(array $data)
    {
        if (isset($data['task'])) {
            if ((!isset($data['ids']) || !is_array($data['ids'])) ||
                (isset($data['ids']) && is_array($data['ids']) && count($data['ids']) === 0)
            ) {
                $this->addResponse('Ids not set', 1);

                return;
            }

            foreach ($data['ids'] as $key => $id) {
                if ($data['task'] === 'read') {
                    $readData = [];
                    $readData['id'] = $id;
                    $this->markRead($readData);
                } else if ($data['task'] === 'archive') {
                    $archiveData = [];
                    $archiveData['id'] = $id;
                    $this->markArchive($archiveData);
                } else if ($data['task'] === 'remove') {
                    $removeData = [];
                    $removeData['id'] = $id;
                    $this->removeNotification($removeData);
                }
            }
        } else {
            $this->addResponse('Task Missing', 1);
        }
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