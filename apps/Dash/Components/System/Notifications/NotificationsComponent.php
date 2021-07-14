<?php

namespace Apps\Dash\Components\System\Notifications;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use System\Base\BaseComponent;

class NotificationsComponent extends BaseComponent
{
    use DynamicTable;

    protected $notifications;

    public function initialize()
    {
        $this->notifications = $this->basepackages->notifications;
    }

    public function fetchNewNotificationsCountAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }
            $this->notifications->fetchNewNotificationsCount();

            $this->addResponse(
                $this->notifications->packagesData->responseMessage,
                $this->notifications->packagesData->responseCode,
                $this->notifications->packagesData->responseData
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function changeStateAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }
            $this->notifications->changeNotificationState($this->postData());

            $this->addResponse(
                $this->notifications->packagesData->responseMessage,
                $this->notifications->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function markReadAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if (isset($this->postData()['bulk'])) {
                $this->notifications->bulk($this->postData());
            } else {
                $this->notifications->markRead($this->postData());
            }

            $this->addResponse(
                $this->notifications->packagesData->responseMessage,
                $this->notifications->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function markArchiveAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if (isset($this->postData()['bulk'])) {
                $this->notifications->bulk($this->postData());
            } else {
                $this->notifications->markArchive($this->postData());
            }

            $this->addResponse(
                $this->notifications->packagesData->responseMessage,
                $this->notifications->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function viewAction()
    {
        $conditions =
            [
                'conditions'    =>
                    '-:app_id:equals:' . $this->apps->getAppInfo()['id'] . '&and:account_id:equals:' . $this->auth->account()['id'] . '&and:read:equals:0&and:archive:equals:0&',
                'order'         => 'id desc'
            ];

        if ($this->request->isPost()) {
            $this->postData()['order'] = 'id desc';
        }

        $replaceColumns =
            function ($dataArr) {
                if ($dataArr && is_array($dataArr) && count($dataArr) > 0) {
                    return $this->replaceColumns($dataArr);
                }

                return $dataArr;
            };

        $this->generateDTContent(
            $this->notifications,
            'system/notifications/view',
            $conditions,
            ['[notification_title]', '[notification_details]', 'package_row_id', 'created_by', 'read'],
            true,
            ['[notification_title]', '[notification_details]', 'package_row_id', 'created_by', 'read'],
            null,
            ['package_row_id' => 'link', 'notification_title' => 'notification', 'notification_details' => 'details', 'created_by' => 'User (at)', 'read' => 'actions'],
            $replaceColumns,
            'notification_title',
            null,
            false,
            null
        );

        $this->view->pick('notifications/list');
    }

    public function removeAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if (isset($this->postData()['bulk'])) {
                $this->notifications->bulk($this->postData());
            } else {
                $this->notifications->removeNotification($this->postData());
            }

            $this->addResponse(
                $this->notifications->packagesData->responseMessage,
                $this->notifications->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    protected function replaceColumns($dataArr)
    {
        foreach ($dataArr as $dataKey => &$data) {
            $data = $this->generateUserInfo($dataKey, $data);
            $data = $this->generateLinkButton($dataKey, $data);
            $data = $this->generateReadButton($dataKey, $data);
            $data = $this->generateArchiveButton($dataKey, $data);
            $data = $this->generateRemoveButton($dataKey, $data);
        }

        return $dataArr;
    }

    protected function packageLinks()
    {
        //This list will grow as packages grow
        return
            [
                'Vendors' => 'business/directory/vendors'
            ];
    }

    protected function generateUserInfo($rowId, $data)
    {
        if ($data['created_by']) {
            $profile = $this->basepackages->profile->getProfile($data['created_by']);

            if ($profile) {
                $data['created_by'] = $profile['full_name'] . ' (' . $data['created_at'] . ')';
            } else {
                $data['created_by'] = 'System (' . $data['created_at'] . ')';
            }
        }

        return $data;
    }

    protected function generateLinkButton($rowId, $data)
    {
        if ($data['package_row_id']) {
            if (array_key_exists($data['package_name'], $this->packageLinks())) {
                $data['package_row_id'] =
                    '<a id="' . strtolower($this->app['route']) . '-' . strtolower($this->componentName) . '-access-' . $rowId . '" href="' .  $this->links->url($this->packageLinks()[$data['package_name']] . '/q/id/' . $data['package_row_id']) . '" type="button" data-id="' . $data['id'] . '" data-rowid="' . $rowId . '" class="text-white btn btn-primary btn-xs rowAccess text-uppercase">
                        <i class="fas fa-fw fa-xs fa-external-link-alt"></i>
                    </a>';
            }
        }

        return $data;
    }

    protected function generateReadButton($rowId, $data)
    {
        if ($data['read'] == 0 && $data['archive'] == 0) {
            $data['read'] =
                '<a id="' . strtolower($this->app['route']) . '-' . strtolower($this->componentName) . '-markread-' . $rowId . '" href="' . $this->links->url('system/notifications/markRead/q/id/' . $data['id']) . '" type="button" data-id="' . $data['id'] . '" data-rowid="' . $rowId . '" class="ml-1 mr-1 pl-2 pr-2 text-white btn btn-info btn-xs rowMarkRead text-uppercase">
                    <i class="mr-1 fas fa-fw fa-xs fa-eye"></i>
                    <span class="text-xs"> Mark Read</span>
                </a>';
        } else {
            $data['read'] = '';
        }

        return $data;
    }

    protected function generateArchiveButton($rowId, $data)
    {
        if ($data['archive'] == 0) {
            $data['read'] .=
                '<a id="' . strtolower($this->app['route']) . '-' . strtolower($this->componentName) . '-markarchive-' . $rowId . '" href="' . $this->links->url('system/notifications/markArchive/q/id/' . $data['id']) . '" type="button" data-id="' . $data['id'] . '" data-rowid="' . $rowId . '" class="ml-1 mr-1 pl-2 pr-2 text-white btn btn-primary btn-xs rowArchive text-uppercase">
                    <i class="mr-1 fas fa-fw fa-xs fa-save"></i>
                    <span class="text-xs"> Archive</span>
                </a>';
        } else {
            $data['archive'] = '';
        }

        return $data;
    }

    protected function generateRemoveButton($rowId, $data)
    {
        $data['read'] .=
            '<a id="' . strtolower($this->app['route']) . '-' . strtolower($this->componentName) . '-remove-__control-' . $rowId . '" href="' . $this->links->url('system/notifications/remove/q/id/' . $data['id']) . '" type="button" data-id="' . $data['id'] . '" data-rowid="' . $rowId . '" class="ml-1 mr-1 pl-2 pr-2 text-white btn btn-danger btn-xs rowRemove text-uppercase" data-notificationtextfromcolumn="notification_title">
                <i class="mr-1 fas fa-fw fa-xs fa-trash"></i>
                <span class="text-xs"> Remove</span>
            </a>';

        return $data;
    }
}