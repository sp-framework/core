<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesActivityLogs;

class ActivityLogs extends BasePackage
{
    protected $modelToUse = BasepackagesActivityLogs::class;

    protected $packageNameS = 'activitylogs';

    public $activityLogs;

    const ACTIVITY_TYPE_ADD = 1;

    const ACTIVITY_TYPE_UPDATE = 2;

    public function init(bool $resetCache = false)
    {
        return $this;
    }

    public function get(array $data = [], $resetCache = false)
    {
        $logsArr = [];

        if (isset($data['new_first']) && $data['new_first'] === true) {
            $order = 'id desc';
        } else {
            $order = 'id asc';
        }

        $pagedLogs = $this->getPaged(
            [
                'conditions'    =>
                    '-|package_name|equals|' . $data['package_name'] . '&and|package_row_id|equals|' . $data['package_row_id'] . '&',
                'order'         => $order,
                'limit'         => 10,
                'page'          => $data['page']
            ]
        );

        if ($pagedLogs) {
            $logsArr = $pagedLogs->getItems();
        }

        if (count($logsArr) > 0) {
            foreach ($logsArr as $key => &$log) {
                unset($log['id']);
                unset($log['package_name']);
                unset($log['package_row_id']);

                if ($log['account_id'] != 0) {
                    $account = $this->basepackages->accounts->get(['id' => $log['account_id']]);
                    $log['account_email'] = $account['email'];

                    $profile = $this->basepackages->profile->get(['account_id' => $log['account_id']]);
                    $log['account_full_name'] = $profile['full_name'];

                    unset($log['account_id']);
                } else {
                    $log['account_email'] = 'N/A';
                    $log['account_full_name'] = 'System';
                }

                if ($log['log'] !== '') {
                    $log['log'] = Json::decode($log['log'], true);
                }
            }

            if ($this->packagesData->paginationCounters) {
                $logsArr = array_replace($logsArr, ['paginationCounters' => $this->packagesData->paginationCounters]);
            }

            return $logsArr;
        }

        return [];
    }

    public function add(array $data)
    {
        $newData = $data['data'];
        $dataId = $newData['id'];
        unset($newData['id']);

        $newData = $this->removeSessionToken($newData);
        if (isset($newData['package_name'])) {
            unset($newData['package_name']);
        }

        if (isset($data['old_data'])) {
            $oldData = $data['old_data'];
            if (isset($oldData['id'])) {
                unset($oldData['id']);
            }
            if (isset($oldData['package_name'])) {
                unset($oldData['package_name']);
            }
            $newData = $this->getDifference($this->jsonData($newData), $this->jsonData($oldData));

            $log['activity_type'] = self::ACTIVITY_TYPE_UPDATE;
        } else {
            $log['activity_type'] = self::ACTIVITY_TYPE_ADD;
        }

        if (PHP_SAPI === 'cli') {
            $log['account_id'] = 0;//System
        } else {
            $account = $this->auth->account();

            if ($account) {
                $log['account_id'] = $account['id'];//User
            } else {
                $log['account_id'] = 0;//System
            }
        }

        $log['package_name'] = $data['package_name'];

        $log['package_row_id'] = $dataId;

        if (isset($data['created_at'])) {
            $log['created_at'] = $newData['created_at'];
        }

        $log['log'] = Json::encode($newData);

        if ($this->addtoDb($log, false)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Activity Log Added';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error Adding Activity Log';
        }
    }

    public function update(array $data)
    {
        return;
    }

    public function remove(array $data)
    {
        return;
    }

    protected function getDifference(array $data, array $oldData)
    {
        return array_diff_assoc($data, $oldData);
    }

    protected function removeSessionToken($data)
    {
        $token = array_keys($data, $this->security->getRequestToken());

        if ($token) {
            unset($data[$token[0]]);
        }

        return $data;
    }
}