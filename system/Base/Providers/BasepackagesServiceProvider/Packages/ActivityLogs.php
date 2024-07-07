<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

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

    public function addLog($packageName, array $data, array $oldData = null)
    {
        $dataId = $data['id'];
        unset($data['id']);

        $data = $this->removeSessionToken($data);
        if (isset($data['package_name'])) {
            unset($data['package_name']);
        }

        if ($oldData) {
            if (isset($oldData['id'])) {
                unset($oldData['id']);
            }
            if (isset($oldData['package_name'])) {
                unset($oldData['package_name']);
            }
            $data = $this->getDifference($this->jsonData($data), $this->jsonData($oldData));

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

        $log['package_name'] = $packageName;

        $log['package_row_id'] = $dataId;

        if (isset($data['created_at'])) {
            $log['created_at'] = $data['created_at'];
        }

        $log['log'] = $this->helper->encode($data);

        if ($this->add($log, false)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Activity Log Added';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error Adding Activity Log';
        }
    }

    public function getLogs($packageName, int $packageRowId, bool $newFirst, $page = 1)
    {
        $logsArr = [];

        if ($newFirst) {
            $order = 'id desc';
        } else {
            $order = 'id asc';
        }

        $pagedLogs = $this->getPaged(
            [
                'conditions'    => '-|package_name|equals|' . $packageName . '&and|package_row_id|equals|' . $packageRowId . '&',
                'order'         => $order,
                'limit'         => 10,
                'page'          => $page
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
                    $account = $this->basepackages->accounts->getById($log['account_id']);
                    $log['account_email'] = $account['email'];

                    $profile = $this->basepackages->profiles->getProfile($log['account_id']);
                    $log['account_full_name'] = $profile['full_name'];

                    unset($log['account_id']);
                } else {
                    $log['account_email'] = 'N/A';
                    $log['account_full_name'] = 'System';
                }

                if ($log['log'] !== '') {
                    $log['log'] = $this->helper->decode($log['log'], true);
                }
            }

            if ($this->packagesData->paginationCounters) {
                $logsArr = array_replace($logsArr, ['paginationCounters' => $this->packagesData->paginationCounters]);
            }

            return $logsArr;
        }

        return [];
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