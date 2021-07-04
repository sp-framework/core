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
        $notifications = count($this->getByParams(
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
        ));

        $this->addResponse('Ok', 0, ['count' => $notifications]);
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
}