<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

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
        $createdBy = null,
        $packageName = null,
        $packageRowId = null,
        $notificationType = 0
    ) {
        if (!$notificationTitle) {
            throw new \Exception('Notification title missing');
        }

        if ($notificationDetails && is_array($notificationDetails)) {
            $notificationDetails = $this->helper->encode($notificationDetails);
        }

        $newNotification = [];
        $newNotification['notification_type'] = $notificationType;
        $newNotification['app_id'] = $appId;
        $newNotification['account_id'] = $accountId;
        if ($createdBy) {
            $newNotification['created_by'] = $createdBy;
        } else {
            if (isset($this->auth) && $this->auth->account()) {
                $newNotification['created_by'] = $this->auth->account()['id'];
            } else {
                $newNotification['created_by'] = '0';
            }
        }
        $newNotification['package_name'] = $packageName;
        $newNotification['package_row_id'] = $packageRowId;
        $newNotification['notification_title'] = $notificationTitle;
        $newNotification['notification_details'] = $notificationDetails;

        if ($this->add($newNotification, false)) {
            $this->pushNotification($newNotification);

            $this->addResponse('Added new notification');
        } else {
            $this->addResponse('Error adding new notification', 1);
        }
    }

    protected function pushNotification($notification)
    {
        $account = $this->basepackages->accounts->getFirst('id', $notification['account_id'], true);

        if (!$account || ($account && !$account->tunnels)) {
            return;
        } else {
            $tunnels = $account->tunnels->toArray();
        }

        if ($tunnels['notifications_tunnel']) {
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
                $this->wss->send(
                    [
                        'type'              => 'systemNotifications',
                        'to'                => $tunnels['notifications_tunnel'],
                        'response'          => [
                            'responseCode'      => 0,
                            'responseData'      =>
                                [
                                    "count" => $count
                                ]
                        ]
                    ]
                );
            }
        }
    }

    public function emailNotification(
        $emailAddresses,
        $notificationTitle = null,
        $notificationDetails = null,
        $domainId = null,
        $appId = null,
        $createdBy = 0,
        $packageName = null,
        $packageRowId = null,
        $notificationType = 0,
        $subject = null,
        $body = null
    ) {
        if (!$body) {
            if (!$notificationTitle) {
                throw new \Exception('Notification requires title if body is not provided.');
            }

            $body = '';

            $body .= 'Notification Title: ' . $notificationTitle . '<br>';

            if ($notificationDetails) {
                $body .= 'Notification Details: ' . $notificationDetails . '<br>';
            }

            $now = date("F j, Y, g:i a");

            if ($createdBy != 0) {
                $profile = $this->basepackages->profile->getProfile($createdBy);

                if ($profile) {
                    $body .= 'Notification By: ' . $profile['full_name'] . ' (' . $now . ')<br>';
                } else {
                    $body .= 'Notification By: System (' . $now . ')<br>';
                }
            } else {
                $body .= 'Notification By: System (' . $now . ')<br>';
            }
        }

        if ($domainId) {
            $domain = $this->domains->getDomainById($domainId);
        } else {
            $domain = $this->domains->getDomain();
        }

        $email['domain_id'] = $domainId;
        $email['app_id'] = $appId;
        $email['status'] = 1;
        $email['priority'] = 3;
        $email['to_addresses'] = $this->helper->encode($emailAddresses);
        if ($subject) {
            $email['subject'] = $subject;
        } else {
            $email['subject'] = 'Notification for ' . $domain['name'];
        }
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

        $total = 0;
        $info = 0;
        $warning = 0;
        $error = 0;

        if ($notifications && is_array($notifications)) {
            $total = count($notifications);
        } else {
            $total = 0;
        }

        if ($total > 0) {
            foreach ($notifications as $notification) {
                if ($notification['notification_type'] == '0') {
                    $info = $info + 1;
                } else if ($notification['notification_type'] == '1') {
                    $warning = $warning + 1;
                } else if ($notification['notification_type'] == '2') {
                    $error = $error + 1;
                }
            }
        }

        $this->addResponse(
            'Ok',
            0,
            [
                'count' =>
                    [
                        'total'     => $total,
                        'info'      => $info,
                        'warning'   => $warning,
                        'error'     => $error
                    ],
                'mute'  => $isMute
            ]
        );
    }

    protected function getNotificationsCount()
    {
        if ($this->config->databasetype === 'db') {
            $conditions =
                [
                    'conditions'    =>
                        'app_id = :appId: AND account_id = :aId: AND read = :read: AND archive = :archive:',
                    'bind'          =>
                        [
                            'appId'         => $this->apps->getAppInfo()['id'],
                            'aId'           => $this->auth->account()['id'],
                            'read'          => 0,
                            'archive'       => 0
                        ]
                ];
        } else {
            $conditions =
                [
                    'conditions'    => [
                        ['app_id', '=', $this->apps->getAppInfo()['id']],
                        ['account_id', '=', $this->auth->account()['id']],
                        ['read', '=', 0],
                        ['archive', '=', 0]
                    ]
                ];
        }

        return $this->getByParams($conditions, false, false);
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

        $profile['settings'] = $this->helper->encode($profile['settings']);

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
                    ], true, false
            ]
        );

        if ($notificationsArr && count($notificationsArr) > 0) {
            foreach ($notificationsArr as $key => &$notification) {
                $notification['notification'] = $this->helper->decode($notification['notification'], true);
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